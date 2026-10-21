<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Concerns\ResolvesVendor;
use App\Http\Controllers\Controller;
use App\Models\VendorService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    use ResolvesVendor;

    public const TYPES = [
        'hotel'     => 'Hotel',
        'transport' => 'Transportasi',
        'guide'     => 'Pemandu wisata',
    ];

    public function index(Request $request): View
    {
        $services = $this->vendorOf($request)->services()
            ->withCount('tourPackages')
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')->toString()))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('manager.services.index', ['services' => $services, 'types' => self::TYPES]);
    }

    public function create(): View
    {
        return view('manager.services.create', ['types' => self::TYPES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->vendorOf($request)->services()->create($this->validated($request));

        return redirect()->route('manager.services.index')
            ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Request $request, VendorService $service): View
    {
        $this->ensureOwned($request, $service);

        return view('manager.services.edit', ['service' => $service, 'types' => self::TYPES]);
    }

    public function update(Request $request, VendorService $service): RedirectResponse
    {
        $this->ensureOwned($request, $service);
        $service->update($this->validated($request));

        return redirect()->route('manager.services.index')
            ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Request $request, VendorService $service): RedirectResponse
    {
        $this->ensureOwned($request, $service);

        if ($service->tourPackages()->exists()) {
            return back()->with('error', 'Layanan masih dipakai oleh paket wisata. Hubungi admin untuk melepasnya.');
        }

        $service->delete();

        return redirect()->route('manager.services.index')
            ->with('success', 'Layanan berhasil dihapus.');
    }

    /** Manager hanya boleh mengelola layanannya sendiri; selain itu tampil 404. */
    private function ensureOwned(Request $request, VendorService $service): void
    {
        abort_unless($service->vendor_id === $this->vendorOf($request)->id, 404);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type'        => ['required', Rule::in(array_keys(self::TYPES))],
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
