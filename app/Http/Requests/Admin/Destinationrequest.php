<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Dipakai untuk membuat dan memperbarui destinasi. */
class DestinationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'country'     => ['required', 'string', 'max:100'],
            'city'        => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'thumbnail'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'        => 'nama destinasi',
            'country'     => 'negara',
            'city'        => 'kota',
            'description' => 'deskripsi',
            'thumbnail'   => 'gambar',
        ];
    }
}