<?php

namespace App\Policies;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    private function isStaff(User $user): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::AGENT], true);
    }

    private function isOwner(User $user, Booking $booking): bool
    {
        return $user->id === $booking->user_id;
    }

    public function view(User $user, Booking $booking): bool
    {
        return $this->isOwner($user, $booking) || $this->isStaff($user);
    }

    /** Upload bukti pembayaran: hanya pemilik, saat booking masih menunggu pembayaran. */
    public function pay(User $user, Booking $booking): bool
    {
        return $this->isOwner($user, $booking) && $booking->status === BookingStatus::PENDING;
    }

    /**
     * Customer hanya bisa membatalkan booking yang belum dibayar.
     * Staff bisa membatalkan selama belum selesai/dibatalkan.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        if ($this->isStaff($user)) {
            return in_array($booking->status, [
                BookingStatus::PENDING,
                BookingStatus::PAID,
                BookingStatus::CONFIRMED,
            ], true);
        }

        return $this->isOwner($user, $booking) && $booking->status === BookingStatus::PENDING;
    }

    /** Ulasan hanya setelah perjalanan selesai. */
    public function review(User $user, Booking $booking): bool
    {
        return $this->isOwner($user, $booking) && $booking->status === BookingStatus::COMPLETED;
    }
}