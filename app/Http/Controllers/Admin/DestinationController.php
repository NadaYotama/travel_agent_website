<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\GeneratesUniqueSlug;
use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestinationController extends Controller
{
    use GeneratesUniqueSlug;

    public function index(Request $request): View
    {
        $destinations = Destination::query()
            ->withCount('tourPackages')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = $request->string('q')->toString();
                $query->where(fn ($q) => $q
                    ->where('name', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhere('country', 'like', "%{$keyword}%"));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.destinations.index', compact('destinations'));
    }

    public function create(): View
    {
        return view('admin.destinations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $data['slug'] = $this->uniqueSlug(Destination::class, $data['name']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('destinations', 'public');
        }

        Destination::create($data);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi berhasil ditambahkan.');
    }

    public function show(Destination $destination): View
    {
        $destination->load(['tourPackages' => fn ($q) => $q->latest()]);

        return view('admin.destinations.show', compact('destination'));
    }

    public function edit(Destination $destination): View
    {
        return view('admin.destinations.edit', compact('destination'));
    }

    public function update(Request $request, Destination $destination): RedirectResponse
    {
        $data = $request->validate($this->rules());

        // Slug sengaja tidak diubah agar URL lama tetap berlaku.
        if ($request->hasFile('thumbnail')) {
            $this->deleteFile($destination->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('destinations', 'public');
        } else {
            unset($data['thumbnail']);
        }

        $destination->update($data);

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi berhasil diperbarui.');
    }

    public function destroy(Destination $destination): RedirectResponse
    {
        if ($destination->tourPackages()->exists()) {
            return back()->with('error', 'Destinasi masih dipakai oleh paket wisata dan tidak dapat dihapus.');
        }

        $this->deleteFile($destination->thumbnail);
        $destination->delete();

        return redirect()->route('admin.destinations.index')
            ->with('success', 'Destinasi berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'country'     => ['required', 'string', 'max:100'],
            'city'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'thumbnail'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    private function deleteFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}