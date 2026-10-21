<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    public function test_admin_can_open_their_profile_from_the_admin_layout(): void
    {
        $admin = User::factory()->make([
            'name'  => 'Admin Test',
            'email' => 'admin@example.test',
            'role'  => UserRole::ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('profile.edit'));

        $response->assertOk()
            ->assertSee('Profil Akun')
            ->assertSee('Admin Test')
            ->assertSee(route('admin.dashboard'))
            ->assertSee(route('profile.update'))
            ->assertSee(route('profile.password'))
            ->assertSee(route('profile.destroy'));
    }
}
