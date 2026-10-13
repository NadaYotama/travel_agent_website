<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GeneratesUniqueSlug;
use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\TourPackage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TourPackageController extends Controller
{
    use GeneratesUniqueSlug;

    public function index(Request $request): View
    {
        $packages = TourPackage::query()
            ->with('destination:id,name')
            ->withCount('bookings')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%' . $request->string('q') . '%'))
            ->when($request->filled('destination_id'), fn ($q) => $q->where('destination_id', $request->integer('destination_id')))
            ->when($request->filled('published'), fn ($q) => $q->where('is_published', $request->boolean('published')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.packages.index', [
            'packages'     => $packages,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.packages.create', [
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        DB::transaction(function () use ($request, $data) {
            $package = TourPackage::create([
                'destination_id' => $data['destination_id'],
                'title'          => $data['title'],
                'slug'           => $this->uniqueSlug(TourPackage::class, $data['title']),
                'description'    => $data['description'],
                'price'          => $data['price'],
                'duration_days'  => $data['duration_days'],
                'quota'          => $data['quota'],
                'start_date'     => $data['start_date'],
                'end_date'       => $data['end_date'],
                'is_published'   => $request->boolean('is_published'),
            ]);

            $this->syncItineraries($package, $data['itineraries'] ?? []);
            $this->storeImages($package, $request->file('images', []));
        });

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket wisata berhasil dibuat.');
    }

    public function show(TourPackage $package): View
    {
        $package->load([
            'destination',
            'itineraries' => fn ($q) => $q->orderBy('day'),
            'images',
            'reviews.user:id,name',
        ]);
        $package->loadCount('bookings');

        return view('admin.packages.show', compact('package'));
    }

    public function edit(TourPackage $package): View
    {
        $package->load(['itineraries' => fn ($q) => $q->orderBy('day'), 'images']);

        return view('admin.packages.edit', [
            'package'      => $package,
            'destinations' => Destination::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, TourPackage $package): RedirectResponse
    {
        $data = $request->validate($this->rules(isUpdate: true));

        DB::transaction(function () use ($request, $data, $package) {
            $package->update([
                'destination_id' => $data['destination_id'],
                'title'          => $data['title'],
                'description'    => $data['description'],
                'price'          => $data['price'],
                'duration_days'  => $data['duration_days'],
                'quota'          => $data['quota'],
                'start_date'     => $data['start_date'],
                'end_date'       => $data['end_date'],
                'is_published'   => $request->boolean('is_published'),
            ]);

            // Hapus foto yang dicentang (hanya milik paket ini)
            if (! empty($data['delete_images'])) {
                $package->images()->whereIn('id', $data['delete_images'])->get()
                    ->each(function ($image) {
                        Storage::disk('public')->delete($image->path);
                        $image->delete();
                    });
            }

            $this->storeImages($package, $request->file('images', []));
            $this->syncItineraries($package, $data['itineraries'] ?? []);
        });

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket wisata berhasil diperbarui.');
    }

    public function destroy(TourPackage $package): RedirectResponse
    {
        if ($package->bookings()->exists()) {
            return back()->with('error', 'Paket sudah memiliki booking. Sembunyikan paket dengan menonaktifkan publikasinya.');
        }

        DB::transaction(function () use ($package) {
            $package->images->each(fn ($image) => Storage::disk('public')->delete($image->path));
            $package->delete();
        });

        return redirect()->route('admin.packages.index')
            ->with('success', 'Paket wisata berhasil dihapus.');
    }

    private function rules(bool $isUpdate = false): array
    {
        return [
            'destination_id'            => ['required', 'exists:destinations,id'],
            'title'                     => ['required', 'string', 'max:255'],
            'description'               => ['required', 'string'],
            'price'                     => ['required', 'numeric', 'min:0'],
            'duration_days'             => ['required', 'integer', 'min:1', 'max:60'],
            'quota'                     => ['required', 'integer', 'min:0'],
            'start_date'                => $isUpdate
                ? ['required', 'date']
                : ['required', 'date', 'after_or_equal:today'],
            'end_date'                  => ['required', 'date', 'after_or_equal:start_date'],
            'is_published'              => ['nullable', 'boolean'],

            'itineraries'               => ['nullable', 'array'],
            'itineraries.*.title'       => ['required', 'string', 'max:255'],
            'itineraries.*.description' => ['nullable', 'string'],

            'images'                    => ['nullable', 'array', 'max:8'],
            'images.*'                  => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'delete_images'             => ['nullable', 'array'],
            'delete_images.*'           => ['integer'],
        ];
    }

    /** Ganti seluruh itinerary; nomor hari mengikuti urutan input. */
    private function syncItineraries(TourPackage $package, array $rows): void
    {
        $package->itineraries()->delete();

        $items = collect($rows)->values()->map(fn (array $row, int $i) => [
            'day'         => $i + 1,
            'title'       => $row['title'],
            'description' => $row['description'] ?? null,
        ])->all();

        if ($items) {
            $package->itineraries()->createMany($items);
        }
    }

    /** @param \Illuminate\Http\UploadedFile[] $files */
    private function storeImages(TourPackage $package, array $files): void
    {
        foreach ($files as $file) {
            $package->images()->create([
                'path' => $file->store('packages', 'public'),
            ]);
        }
    }
}