<?php

namespace App\Http\Requests\Manager;

use App\Models\VendorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Dipakai untuk membuat dan memperbarui layanan manager. */
class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // kepemilikan layanan dicek di Manager\ServiceController::ensureOwned()
    }

    public function rules(): array
    {
        return [
            'type'        => ['required', Rule::in(array_keys(VendorService::TYPES))],
            'name'        => ['required', 'string', 'max:255'],
            'price'       => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'type'        => 'jenis layanan',
            'name'        => 'nama layanan',
            'price'       => 'harga',
            'description' => 'deskripsi',
        ];
    }
}