<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Tests\TestCase;

class TourGuideProfileTest extends TestCase
{
    public function test_tour_guide_can_open_profile_from_their_dashboard_layout(): void
    {
        $tourGuide = User::factory()->make([
            'name'  => 'Tour Guide Test',
            'email' => 'guide@example.test',
            'role'  => UserRole::TOUR_GUIDE,
        ]);

        $response = $this->actingAs($tourGuide)->get(route('profile.edit'));

        $response->assertOk()
            ->assertSee('Profil Akun')
            ->assertSee('Tour Guide Test')
            ->assertSee(route('profile.edit'))
            ->assertSee(route('tourguide.dashboard'))
            ->assertSee(route('profile.update'))
            ->assertSee(route('profile.password'))
            ->assertSee(route('profile.destroy'));
    }

    public function test_tour_guide_is_redirected_to_their_dashboard_from_manager_routes(): void
    {
        $tourGuide = User::factory()->make([
            'role' => UserRole::TOUR_GUIDE,
        ]);

        $this->actingAs($tourGuide)
            ->get(route('manager.dashboard'))
            ->assertRedirect(route('tourguide.dashboard'));
    }

    public function test_tour_guide_dashboard_disallows_browser_caching(): void
    {
        $tourGuide = User::factory()->make([
            'role' => UserRole::TOUR_GUIDE,
        ]);

        $response = $this->actingAs($tourGuide)->get(route('tourguide.dashboard'));

        $response->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
    }
}
