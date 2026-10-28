<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EnquirySavedPropertyTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $role = 'buyer', string $status = 'active'): User
    {
        return User::create([
            'full_name' => ucfirst($role) . ' Test User',
            'email' => $role . uniqid() . '@naijaprops.ng',
            'password' => Hash::make('password'),
            'phone' => '+234 803 000 1111',
            'role' => $role,
            'status' => $status,
        ]);
    }

    private function createProperty(User $owner, array $overrides = []): Property
    {
        return Property::create(array_merge([
            'owner_id' => $owner->id,
            'title' => '4 Bedroom Detached Duplex in Lekki Phase 1',
            'description' => 'Luxury 4 bedroom detached duplex with BQ and swimming pool in Lekki Phase 1, Lagos.',
            'property_type' => 'duplex',
            'transaction_type' => 'sale',
            'price' => 250000000,
            'location' => 'Lekki Phase 1, Lagos',
            'bedrooms' => 4,
            'bathrooms' => 5,
            'status' => 'available',
        ], $overrides));
    }

    public function test_authenticated_user_can_save_and_unsave_a_property(): void
    {
        $owner = $this->createUser('owner');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner);

        // 1. Save property via toggle
        $this->actingAs($buyer)
            ->post(route('saved-properties.toggle', $property))
            ->assertRedirect();

        $this->assertDatabaseHas('saved_properties', [
            'user_id' => $buyer->id,
            'property_id' => $property->id,
        ]);

        // 2. View saved properties page
        $this->actingAs($buyer)
            ->get(route('saved-properties.index'))
            ->assertOk()
            ->assertSee($property->title)
            ->assertSee('₦250,000,000');

        // 3. Unsave property via toggle
        $this->actingAs($buyer)
            ->post(route('saved-properties.toggle', $property))
            ->assertRedirect();

        $this->assertDatabaseMissing('saved_properties', [
            'user_id' => $buyer->id,
            'property_id' => $property->id,
        ]);
    }

    public function test_authenticated_user_can_remove_saved_property_via_delete_endpoint(): void
    {
        $owner = $this->createUser('owner');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner);

        SavedProperty::create([
            'user_id' => $buyer->id,
            'property_id' => $property->id,
        ]);

        $this->actingAs($buyer)
            ->delete(route('saved-properties.destroy', $property))
            ->assertRedirect();

        $this->assertDatabaseMissing('saved_properties', [
            'user_id' => $buyer->id,
            'property_id' => $property->id,
        ]);
    }

    public function test_guest_cannot_save_property_or_view_saved_list(): void
    {
        $owner = $this->createUser('owner');
        $property = $this->createProperty($owner);

        $this->get(route('saved-properties.index'))->assertRedirect(route('login'));
        $this->post(route('saved-properties.toggle', $property))->assertRedirect(route('login'));
    }

    public function test_buyer_can_send_enquiry_about_a_property(): void
    {
        $owner = $this->createUser('owner');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner);

        $message = 'Good day, is this 4 bedroom duplex in Lekki Phase 1 available for inspection on Saturday?';

        $this->actingAs($buyer)
            ->post(route('enquiries.store', $property), [
                'message' => $message,
            ])
            ->assertRedirect(route('properties.show', $property));

        $this->assertDatabaseHas('enquiries', [
            'property_id' => $property->id,
            'user_id' => $buyer->id,
            'message' => $message,
            'status' => 'pending',
        ]);

        // Buyer can view their sent enquiry in the enquiries page
        $this->actingAs($buyer)
            ->get(route('enquiries.index'))
            ->assertOk()
            ->assertSee($message)
            ->assertSee($property->title);
    }

    public function test_enquiry_validation_requires_minimum_message_length(): void
    {
        $owner = $this->createUser('owner');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner);

        $this->actingAs($buyer)
            ->post(route('enquiries.store', $property), [
                'message' => 'Too short',
            ])
            ->assertSessionHasErrors('message');

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_property_owner_cannot_send_enquiry_on_own_property(): void
    {
        $owner = $this->createUser('owner');
        $property = $this->createProperty($owner);

        $this->actingAs($owner)
            ->post(route('enquiries.store', $property), [
                'message' => 'Trying to send an enquiry to my own property listing.',
            ])
            ->assertForbidden();
    }

    public function test_property_owner_can_view_and_update_status_of_enquiries_on_their_listings(): void
    {
        $owner = $this->createUser('owner');
        $otherOwner = $this->createUser('agent');
        $buyer = $this->createUser('buyer');
        $property = $this->createProperty($owner);

        $enquiry = Enquiry::create([
            'property_id' => $property->id,
            'user_id' => $buyer->id,
            'message' => 'Hello, I want to enquire about the payment plan and C of O documents.',
            'status' => 'pending',
        ]);

        // Owner sees the enquiry on the received tab
        $this->actingAs($owner)
            ->get(route('enquiries.index', ['tab' => 'received']))
            ->assertOk()
            ->assertSee($enquiry->message)
            ->assertSee($buyer->full_name);

        // Owner updates status to replied
        $this->actingAs($owner)
            ->patch(route('enquiries.update-status', $enquiry), [
                'status' => 'replied',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('enquiries', [
            'id' => $enquiry->id,
            'status' => 'replied',
        ]);

        // Buyer or unrelated owner cannot update status
        $this->actingAs($buyer)
            ->patch(route('enquiries.update-status', $enquiry), ['status' => 'closed'])
            ->assertForbidden();

        $this->actingAs($otherOwner)
            ->patch(route('enquiries.update-status', $enquiry), ['status' => 'closed'])
            ->assertForbidden();
    }

    public function test_sender_or_property_owner_can_delete_enquiry_but_unrelated_user_cannot(): void
    {
        $owner = $this->createUser('owner');
        $buyer = $this->createUser('buyer');
        $stranger = $this->createUser('buyer');
        $property = $this->createProperty($owner);

        $enquiry = Enquiry::create([
            'property_id' => $property->id,
            'user_id' => $buyer->id,
            'message' => 'Please is the service charge inclusive of diesel?',
            'status' => 'pending',
        ]);

        $this->actingAs($stranger)
            ->delete(route('enquiries.destroy', $enquiry))
            ->assertForbidden();

        $this->actingAs($buyer)
            ->delete(route('enquiries.destroy', $enquiry))
            ->assertRedirect();

        $this->assertDatabaseMissing('enquiries', [
            'id' => $enquiry->id,
        ]);
    }
}
