<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN  = 'admin';
    case AGENT  = 'agent';
    case VENDOR = 'vendor';
    case USER   = 'user';

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::ADMIN  => 'admin.dashboard',
            self::AGENT  => 'agent.dashboard',
            self::VENDOR => 'vendor.dashboard',
            self::USER   => 'user.home',
        };
    }
}