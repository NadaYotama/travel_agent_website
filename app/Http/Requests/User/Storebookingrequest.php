<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // kebijakan paket dicek di controller lewat TourPackagePolicy
    }

    public function rules(): array
    {
        return [
            'participants'             => ['required', 'array', 'min:1', 'max:20'],
            'participants.*.name'      => ['required', 'string', 'max:255'],
            'participants.*.id_number' => ['nullable', 'string', 'max:30'],
            'participants.*.phone'     => ['nullable', 'string', 'max:20'],
            'promo_code'               => ['nullable', 'string', 'max:30'],
            'notes'                    => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'participants'             => 'peserta',
            'participants.*.name'      => 'nama peserta',
            'participants.*.id_number' => 'nomor identitas',
            'participants.*.phone'     => 'nomor telepon peserta',
            'promo_code'               => 'kode promo',
            'notes'                    => 'catatan',
        ];
    }

    public function messages(): array
    {
        return [
            'participants.required' => 'Tambahkan minimal satu peserta.',
            'participants.min'      => 'Tambahkan minimal satu peserta.',
        ];
    }
}