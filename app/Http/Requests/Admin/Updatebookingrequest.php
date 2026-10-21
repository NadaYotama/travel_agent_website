<?php

namespace App\Http\Requests\Admin;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'   => ['required', Rule::enum(BookingStatus::class)],
            'agent_id' => [
                'nullable',
                Rule::exists('users', 'id')->whereIn('role', [UserRole::TOUR_GUIDE->value, UserRole::ADMIN->value]),
            ],
            'notes'    => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'status'   => 'status booking',
            'agent_id' => 'tour guide',
            'notes'    => 'catatan',
        ];
    }
}