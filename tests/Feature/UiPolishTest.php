<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiPolishTest extends TestCase
{
    use RefreshDatabase;

    public function test_favicon_and_theme_toggle_with_local_storage_are_present_in_layout(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('favicon.svg', false);
        $response->assertSee('favicon.ico', false);
        $response->assertSee('naijaprops_theme', false);
        $response->assertSee('toggleTheme()', false);

        $this->assertFileExists(public_path('favicon.svg'));
        $this->assertFileExists(public_path('favicon.ico'));
    }

    public function test_user_dashboard_renders_responsive_collapsible_user_sidebar(): void
    {
        $owner = User::create([
            'full_name' => 'Chinedu Okafor',
            'email' => 'chinedu@example.com',
            'password' => 'password123',
            'phone' => '08030000001',
            'role' => 'owner',
            'status' => 'active',
        ]);

        $response = $this->actingAs($owner)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('User Dashboard Sidebar', false);
        $response->assertSee('toggleSidebarCollapse()', false);
        $response->assertSee('naijaprops_sidebar_collapsed', false);
        $response->assertSee('mobileSidebarOpen', false);
        $response->assertSee('My Listings');
        $response->assertSee('Saved Properties');
        $response->assertSee('Enquiries');
    }

    public function test_admin_dashboard_renders_responsive_collapsible_admin_sidebar(): void
    {
        $admin = User::create([
            'full_name' => 'Amina Bello',
            'email' => 'admin@naijaprops.ng',
            'password' => 'password123',
            'phone' => '08090000009',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Admin Dashboard Sidebar', false);
        $response->assertSee('Admin Control Center');
        $response->assertSee('toggleSidebarCollapse()', false);
        $response->assertSee('Manage Properties');
        $response->assertSee('Manage Users');
        $response->assertSee('Listing Reports');
    }
}
