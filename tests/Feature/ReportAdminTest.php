<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportAdminTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $role = 'buyer', string $status = 'active'): User
    {
        return User::create([
            'full_name' => ucfirst($role) . ' Test User',
            'email' => $role . uniqid() . '@naijaprops.ng',
            'password' => Hash::make('password'),
            'phone' => '+234 803 000 2222',
            'role' => $role,
            'status' => $status,
        ]);
    }

    private function createProperty(User $owner, array $overrides = []): Property
    {
        return Property::create(array_merge([
            'owner_id' => $owner->id,
            'title' => '4 Bedroom Detached Duplex in Old GRA Port Harcourt',
            'description' => 'Executive 4 bedroom detached duplex with security house in Old GRA, Port Harcourt.',
            'property_type' => 'duplex',
            'transaction_type' => 'sale',
            'price' => 145000000,
            'location' => 'Old GRA, Port Harcourt',
            'bedrooms' => 4,
            'bathrooms' => 5,
            'status' => 'available',
        ], $overrides));
    }

    public function test_authenticated_user_can_report_a_suspicious_property_listing(): void
    {
        $owner = $this->createUser('agent');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner);

        $reason = 'Agent requested an upfront inspection fee of ₦20,000 before physical viewing in Old GRA.';

        $this->actingAs($buyer)
            ->post(route('reports.store', $property), [
                'reason' => $reason,
            ])
            ->assertRedirect(route('properties.show', $property));

        $this->assertDatabaseHas('reports', [
            'property_id' => $property->id,
            'reported_by' => $buyer->id,
            'reason' => $reason,
            'status' => 'pending',
        ]);

        // Buyer sees their submitted report on the property details page
        $this->actingAs($buyer)
            ->get(route('properties.show', $property))
            ->assertOk()
            ->assertSee('Your Submitted Report')
            ->assertSee($reason);
    }

    public function test_report_submission_validates_minimum_reason_length(): void
    {
        $owner = $this->createUser('owner');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner);

        $this->actingAs($buyer)
            ->post(route('reports.store', $property), [
                'reason' => 'Scam',
            ])
            ->assertSessionHasErrors('reason');

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_property_owner_cannot_report_their_own_listing(): void
    {
        $owner = $this->createUser('owner');
        $property = $this->createProperty($owner);

        $this->actingAs($owner)
            ->post(route('reports.store', $property), [
                'reason' => 'Trying to report my own property listing.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('reports', 0);
    }

    public function test_duplicate_pending_report_from_same_user_updates_existing_report(): void
    {
        $owner = $this->createUser('owner');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner);

        $this->actingAs($buyer)->post(route('reports.store', $property), [
            'reason' => 'Initial report reason about suspicious inspection fee.',
        ]);

        $this->actingAs($buyer)->post(route('reports.store', $property), [
            'reason' => 'Updated report reason with phone number and extra details.',
        ]);

        $this->assertDatabaseCount('reports', 1);
        $this->assertDatabaseHas('reports', [
            'property_id' => $property->id,
            'reported_by' => $buyer->id,
            'reason' => 'Updated report reason with phone number and extra details.',
        ]);
    }

    public function test_non_admin_users_cannot_access_admin_dashboard_or_reports(): void
    {
        $owner = $this->createUser('owner');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner);

        foreach ([$owner, $buyer] as $nonAdmin) {
            $this->actingAs($nonAdmin)->get(route('admin.dashboard'))->assertForbidden();
            $this->actingAs($nonAdmin)->get(route('admin.users.index'))->assertForbidden();
            $this->actingAs($nonAdmin)->get(route('admin.properties.index'))->assertForbidden();
            $this->actingAs($nonAdmin)->get(route('admin.reports.index'))->assertForbidden();
            $this->actingAs($nonAdmin)->patch(route('admin.properties.approve', $property))->assertForbidden();
        }
    }

    public function test_admin_can_view_dashboard_with_statistics_and_reports_page(): void
    {
        $admin = $this->createUser('admin');
        $owner = $this->createUser('owner');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner, [
            'location' => 'Lekki Phase 1, Lagos',
        ]);

        $report = Report::create([
            'property_id' => $property->id,
            'reported_by' => $buyer->id,
            'reason' => 'Misleading photos on Lekki Phase 1 listing.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('NaijaProps Admin Control Center')
            ->assertSee('Misleading photos on Lekki Phase 1 listing.')
            ->assertSee('Lagos: 1');

        $this->actingAs($admin)
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Suspicious Listing Reports')
            ->assertSee($report->reason)
            ->assertSee($property->title);
    }

    public function test_admin_can_update_report_status_and_moderate_reported_property(): void
    {
        $admin = $this->createUser('admin');
        $owner = $this->createUser('agent');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner, ['status' => 'available']);

        $report = Report::create([
            'property_id' => $property->id,
            'reported_by' => $buyer->id,
            'reason' => 'Property appears fraudulent; agent asked for payment before inspection.',
            'status' => 'pending',
        ]);

        // 1. Admin marks report as reviewed and places the property on hold (reject -> pending)
        $this->actingAs($admin)
            ->patch(route('admin.reports.update-status', $report), [
                'status' => 'reviewed',
                'property_action' => 'reject',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'reviewed',
        ]);
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => 'pending',
        ]);

        // 2. Admin resolves report and approves the property back to available
        $this->actingAs($admin)
            ->patch(route('admin.reports.update-status', $report), [
                'status' => 'resolved',
                'property_action' => 'approve',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'resolved',
        ]);
        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => 'available',
        ]);

        // 3. Admin can remove the reported property completely via report moderation
        $this->actingAs($admin)
            ->patch(route('admin.reports.update-status', $report), [
                'status' => 'resolved',
                'property_action' => 'remove',
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }

    public function test_admin_can_approve_and_reject_properties_and_delete_report(): void
    {
        $admin = $this->createUser('admin');
        $owner = $this->createUser('owner');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner, ['status' => 'available']);

        // Reject / Hold property
        $this->actingAs($admin)
            ->patch(route('admin.properties.reject', $property))
            ->assertRedirect();

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => 'pending',
        ]);

        // Approve property
        $this->actingAs($admin)
            ->patch(route('admin.properties.approve', $property))
            ->assertRedirect();

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => 'available',
        ]);

        // Delete a report
        $report = Report::create([
            'property_id' => $property->id,
            'reported_by' => $buyer->id,
            'reason' => 'Accidental duplicate report by user.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.reports.destroy', $report))
            ->assertRedirect();

        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }
}
