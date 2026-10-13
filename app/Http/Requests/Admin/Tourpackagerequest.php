<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Dipakai untuk membuat dan memperbarui paket wisata (beserta itinerary, foto, dan layanan vendor). */
class TourPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'destination_id'            => ['required', 'exists:destinations,id'],
            'title'                     => ['required', 'string', 'max:255'],
            'description'               => ['required', 'string'],
            'price'                     => ['required', 'numeric', 'min:0'],
            'duration_days'             => ['required', 'integer', 'min:1', 'max:60'],
            'quota'                     => ['required', 'integer', 'min:0'],
            // Tanggal berangkat boleh di masa lalu saat edit (paket yang sudah berjalan)
            'start_date'                => $isUpdate
                ? ['required', 'date']
                : ['required', 'date', 'after_or_equal:today'],
            'end_date'                  => ['required', 'date', 'after_or_equal:start_date'],
            'is_published'              => ['nullable', 'boolean'],

            'itineraries'               => ['nullable', 'array'],
            'itineraries.*.title'       => ['required', 'string', 'max:255'],
            'itineraries.*.description' => ['nullable', 'string'],

            'vendor_service_ids'        => ['nullable', 'array'],
            'vendor_service_ids.*'      => ['integer', 'exists:vendor_services,id'],

            'images'                    => ['nullable', 'array', 'max:8'],
            'images.*'                  => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'delete_images'             => ['nullable', 'array'],
            'delete_images.*'           => ['integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'destination_id'            => 'destinasi',
            'title'                     => 'judul paket',
            'description'               => 'deskripsi',
            'price'                     => 'harga',
            'duration_days'             => 'durasi (hari)',
            'quota'                     => 'kuota',
            'start_date'                => 'tanggal berangkat',
            'end_date'                  => 'tanggal pulang',
            'itineraries.*.title'       => 'judul itinerary',
            'images.*'                  => 'foto',
        ];
    }
}