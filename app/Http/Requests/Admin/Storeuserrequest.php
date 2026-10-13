<?php

namespace App\Http\Requests\Admin;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses dibatasi middleware role:admin pada route
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'role'      => ['required', Rule::enum(UserRole::class)],
            'password'  => ['required', 'confirmed', Password::min(8)],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'      => 'nama',
            'email'     => 'email',
            'phone'     => 'nomor telepon',
            'role'      => 'peran',
            'password'  => 'kata sandi',
            'is_active' => 'status aktif',
        ];
    }
}