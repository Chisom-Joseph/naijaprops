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

    public function test_loading_experience_button_spinners_skeletons_and_overlay_render_correctly(): void
    {
        // 1. Login & Register button spinners and loading overlay attributes
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertOk();
        $loginResponse->assertSee('data-loading-text="Signing in..."', false);
        $loginResponse->assertSee('data-loading-overlay="true"', false);
        $loginResponse->assertSee('np-loading-overlay', false);
        $loginResponse->assertSee('np-top-progress-bar', false);

        $registerResponse = $this->get(route('register'));
        $registerResponse->assertOk();
        $registerResponse->assertSee('data-loading-text="Creating account..."', false);
        $registerResponse->assertSee('data-loading-overlay="true"', false);

        // 2. Property Cards skeleton loader on properties index
        $propertiesResponse = $this->get(route('properties.index'));
        $propertiesResponse->assertOk();
        $propertiesResponse->assertSee('data-np-skeleton', false);
        $propertiesResponse->assertSee('np-skeleton-shimmer', false);
        $propertiesResponse->assertSee('data-loading-text="Filtering..."', false);

        // 3. Admin Tables & Lists skeleton loaders
        $admin = User::create([
            'full_name' => 'Admin Loader Test',
            'email' => 'admin.loader@naijaprops.ng',
            'password' => 'password123',
            'phone' => '08090000010',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $adminPropsResponse = $this->actingAs($admin)->get(route('admin.properties.index'));
        $adminPropsResponse->assertOk();
        $adminPropsResponse->assertSee('data-np-skeleton', false);
        $adminPropsResponse->assertSee('np-skeleton-shimmer', false);

        $adminReportsResponse = $this->actingAs($admin)->get(route('admin.reports.index'));
        $adminReportsResponse->assertOk();
        $adminReportsResponse->assertSee('data-np-skeleton', false);
        $adminReportsResponse->assertSee('np-skeleton-shimmer', false);
    }

    public function test_smooth_scroll_animations_micro_interactions_and_empty_states_render(): void
    {
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertOk();
        $homeResponse->assertSee('scroll-smooth', false);
        $homeResponse->assertSee('scrollToTop()', false);
        $homeResponse->assertSee('IntersectionObserver', false);
        $homeResponse->assertSee('data-animate="fade-up"', false);
        $homeResponse->assertSee('np-card-hover', false);
        $homeResponse->assertSee('np-btn-interactive', false);
        $homeResponse->assertSee('#featured-properties', false);

        // Filtered properties index empty state with city shortcuts & reset action
        $emptySearchResponse = $this->get(route('properties.index', ['location' => 'NonExistentCity123']));
        $emptySearchResponse->assertOk();
        $emptySearchResponse->assertSee('No Matching Properties Found');
        $emptySearchResponse->assertSee('Reset All Filters');
        $emptySearchResponse->assertSee('Try AI Recommendations Instead');
        $emptySearchResponse->assertSee('Lagos Properties');
        $emptySearchResponse->assertSee('Abuja Properties');
        $emptySearchResponse->assertSee('Port Harcourt Properties');
    }
}
