<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_role_list_and_dashboard_destinations_match_the_current_roles(): void
    {
        $this->assertSame(
            ['admin', 'tourguide', 'manager', 'user'],
            array_map(fn (UserRole $role) => $role->value, UserRole::cases())
        );

        $this->assertSame('admin.dashboard', UserRole::ADMIN->dashboardRoute());
        $this->assertSame('tourguide.dashboard', UserRole::TOUR_GUIDE->dashboardRoute());
        $this->assertSame('manager.dashboard', UserRole::MANAGER->dashboardRoute());
        $this->assertSame('user.home', UserRole::USER->dashboardRoute());
    }
}
