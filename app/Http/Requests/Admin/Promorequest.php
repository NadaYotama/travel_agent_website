<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Dipakai untuk membuat dan memperbarui promo. Kode otomatis diubah ke huruf besar. */
class PromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->input('code')))]);
        }
    }

    public function rules(): array
    {
        $valueRules = ['required', 'numeric', 'min:1'];

        // Diskon persen tidak boleh lebih dari 100
        if ($this->input('discount_type') === 'percent') {
            $valueRules[] = 'max:100';
        }

        return [
            'code'           => ['required', 'string', 'max:30', 'alpha_dash', Rule::unique('promos', 'code')->ignore($this->route('promo'))],
            'discount_type'  => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value' => $valueRules,
            'valid_until'    => ['required', 'date', 'after_or_equal:today'],
            'quota'          => ['required', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'code'           => 'kode promo',
            'discount_type'  => 'jenis diskon',
            'discount_value' => 'nilai diskon',
            'valid_until'    => 'berlaku sampai',
            'quota'          => 'kuota',
        ];
    }
}