<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN      = 'admin';
    case TOUR_GUIDE = 'tourguide';
    case MANAGER    = 'manager';
    case USER       = 'user';

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::ADMIN      => 'admin.dashboard',
            self::TOUR_GUIDE => 'tourguide.dashboard',
            self::MANAGER    => 'manager.dashboard',
            self::USER       => 'user.home',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN      => 'Admin',
            self::TOUR_GUIDE => 'Tour Guide',
            self::MANAGER    => 'Manager',
            self::USER       => 'User/Customer',
        };
    }
}