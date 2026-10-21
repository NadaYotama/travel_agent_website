<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Keputusan verifikasi pembayaran. Dipakai juga oleh Agent\BookingController. */
class VerifyPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses dibatasi middleware role:admin / role:tourguide pada route
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::in(['approve', 'reject'])],
        ];
    }
}