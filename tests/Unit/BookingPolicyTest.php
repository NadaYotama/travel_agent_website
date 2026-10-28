<?php

namespace Tests\Unit;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;
use App\Policies\BookingPolicy;
use Tests\TestCase;

class BookingPolicyTest extends TestCase
{
    public function test_customer_can_cancel_pending_and_paid_bookings(): void
    {
        $customer = new User(['role' => UserRole::USER->value]);
        $customer->id = 10;
        $policy = new BookingPolicy;

        foreach ([BookingStatus::PENDING, BookingStatus::PAID, BookingStatus::CONFIRMED] as $status) {
            $booking = new Booking([
                'user_id' => 10,
                'status' => $status->value,
            ]);

            $this->assertTrue($policy->cancel($customer, $booking));
        }
    }

    public function test_customer_cannot_cancel_completed_or_cancelled_bookings(): void
    {
        $customer = new User(['role' => UserRole::USER->value]);
        $customer->id = 10;
        $policy = new BookingPolicy;

        foreach ([BookingStatus::COMPLETED, BookingStatus::CANCELLED] as $status) {
            $booking = new Booking([
                'user_id' => 10,
                'status' => $status->value,
            ]);

            $this->assertFalse($policy->cancel($customer, $booking));
        }
    }
}
