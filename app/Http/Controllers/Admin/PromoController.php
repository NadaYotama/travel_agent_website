<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PromoController extends Controller
{
    public function index(Request $request): View
    {
        $promos = Promo::query()
            ->withCount('bookings')
            ->when($request->filled('q'), fn ($q) => $q->where('code', 'like', '%' . $request->string('q') . '%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.promos.index', compact('promos'));
    }

    public function create(): View
    {
        return view('admin.promos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Promo::create($this->validated($request));

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil dibuat.');
    }

    public function show(Promo $promo): RedirectResponse
    {
        return redirect()->route('admin.promos.edit', $promo);
    }

    public function edit(Promo $promo): View
    {
        return view('admin.promos.edit', compact('promo'));
    }

    public function update(Request $request, Promo $promo): RedirectResponse
    {
        $promo->update($this->validated($request, $promo));

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        // Booking lama tetap aman karena promo_id di-set null (nullOnDelete).
        $promo->delete();

        return redirect()->route('admin.promos.index')
            ->with('success', 'Promo berhasil dihapus.');
    }

    private function validated(Request $request, ?Promo $promo = null): array
    {
        // Kode promo selalu huruf besar
        $request->merge(['code' => strtoupper((string) $request->input('code'))]);

        $valueRules = ['required', 'numeric', 'min:1'];
        if ($request->input('discount_type') === 'percent') {
            $valueRules[] = 'max:100';
        }

        return $request->validate([
            'code'           => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique('promos', 'code')->ignore($promo?->id)],
            'discount_type'  => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value' => $valueRules,
            'valid_until'    => ['required', 'date', 'after_or_equal:today'],
            'quota'          => ['required', 'integer', 'min:0'],
        ]);
    }
}