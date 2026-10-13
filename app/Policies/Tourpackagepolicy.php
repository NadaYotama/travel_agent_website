<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\TourPackage;
use App\Models\User;

class TourPackagePolicy
{
    /** Tamu (belum login) boleh melihat paket yang dipublikasikan. */
    public function view(?User $user, TourPackage $package): bool
    {
        if ($package->is_published) {
            return true;
        }

        return $user !== null && in_array($user->role, [UserRole::ADMIN, UserRole::AGENT], true);
    }

    public function manage(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }
}