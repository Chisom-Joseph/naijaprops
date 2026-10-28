<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyCrudTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $role = 'owner', string $status = 'active'): User
    {
        return User::create([
            'full_name' => ucfirst($role) . ' Test User',
            'email' => $role . uniqid() . '@naijaprops.ng',
            'password' => 'password',
            'phone' => '+234 803 000 1111',
            'role' => $role,
            'status' => $status,
        ]);
    }

    public function test_anyone_can_browse_properties_and_view_property_details(): void
    {
        $owner = $this->createUser('owner');
        $property = Property::create([
            'owner_id' => $owner->id,
            'title' => '4 Bedroom Duplex in Lekki Phase 1',
            'description' => 'Spacious 4 bedroom detached duplex with boys quarters and swimming pool in Lekki Phase 1.',
            'property_type' => 'duplex',
            'transaction_type' => 'sale',
            'price' => 250000000,
            'location' => 'Lekki Phase 1, Lagos',
            'bedrooms' => 4,
            'bathrooms' => 5,
            'status' => 'available',
        ]);

        $this->get(route('properties.index'))
            ->assertOk()
            ->assertSee('4 Bedroom Duplex in Lekki Phase 1')
            ->assertSee('₦250,000,000');

        $this->get(route('properties.show', $property))
            ->assertOk()
            ->assertSee('4 Bedroom Duplex in Lekki Phase 1')
            ->assertSee('Lekki Phase 1, Lagos');
    }

    public function test_owner_and_agent_can_create_property_with_multiple_images(): void
    {
        Storage::fake('public');

        $agent = $this->createUser('agent');

        $image1 = UploadedFile::fake()->image('front-view.jpg', 800, 600);
        $image2 = UploadedFile::fake()->image('living-room.png', 800, 600);

        $response = $this->actingAs($agent)->post(route('properties.store'), [
            'title' => '3 Bedroom Luxury Flat in Victoria Island',
            'description' => 'Serviced 3 bedroom apartment with ocean view, 24/7 power, and gym in Victoria Island, Lagos.',
            'property_type' => 'apartment',
            'transaction_type' => 'rent',
            'price' => 15000000,
            'location' => 'Victoria Island, Lagos',
            'bedrooms' => 3,
            'bathrooms' => 4,
            'status' => 'available',
            'images' => [$image1, $image2],
        ]);

        $property = Property::where('title', '3 Bedroom Luxury Flat in Victoria Island')->firstOrFail();

        $response->assertRedirect(route('properties.show', $property));
        $this->assertEquals($agent->id, $property->owner_id);
        $this->assertCount(2, $property->images);

        foreach ($property->images as $img) {
            $this->assertStringStartsWith('properties/', $img->image_path);
            Storage::disk('public')->assertExists($img->image_path);
        }
    }

    public function test_buyer_cannot_create_property_listing(): void
    {
        $buyer = $this->createUser('buyer');

        $this->actingAs($buyer)
            ->get(route('properties.create'))
            ->assertForbidden();

        $this->actingAs($buyer)
            ->post(route('properties.store'), [
                'title' => 'Unauthorized Listing in Abuja',
                'description' => 'This listing should not be created by a buyer account.',
                'property_type' => 'house',
                'transaction_type' => 'sale',
                'price' => 90000000,
                'location' => 'Maitama, Abuja',
            ])
            ->assertForbidden();
    }

    public function test_owner_can_update_and_delete_own_property_and_manage_images(): void
    {
        Storage::fake('public');

        $owner = $this->createUser('owner');
        $property = Property::create([
            'owner_id' => $owner->id,
            'title' => 'Old Title in Port Harcourt',
            'description' => 'Initial description for this Port Harcourt duplex in Old GRA.',
            'property_type' => 'duplex',
            'transaction_type' => 'sale',
            'price' => 120000000,
            'location' => 'Old GRA, Port Harcourt',
            'bedrooms' => 4,
            'bathrooms' => 4,
            'status' => 'available',
        ]);

        $oldPath = UploadedFile::fake()->image('old.jpg')->store('properties', 'public');
        $oldImage = PropertyImage::create([
            'property_id' => $property->id,
            'image_path' => $oldPath,
            'uploaded_at' => now(),
        ]);

        $newUpload = UploadedFile::fake()->image('new-photo.webp', 800, 600);

        $this->actingAs($owner)
            ->put(route('properties.update', $property), [
                'title' => 'Updated 4 Bedroom Executive Duplex in Old GRA',
                'description' => 'Updated detailed description with swimming pool and security house in Old GRA, Port Harcourt.',
                'property_type' => 'duplex',
                'transaction_type' => 'sale',
                'price' => 135000000,
                'location' => 'Old GRA, Port Harcourt',
                'bedrooms' => 4,
                'bathrooms' => 5,
                'status' => 'pending',
                'remove_images' => [$oldImage->id],
                'images' => [$newUpload],
            ])
            ->assertRedirect(route('properties.show', $property));

        $property->refresh();
        $this->assertEquals('Updated 4 Bedroom Executive Duplex in Old GRA', $property->title);
        $this->assertEquals('pending', $property->status);
        $this->assertDatabaseMissing('property_images', ['id' => $oldImage->id]);
        Storage::disk('public')->assertMissing($oldPath);
        $this->assertCount(1, $property->images);

        // Delete property and verify image cleanup
        $remainingPath = $property->images->first()->image_path;
        $this->actingAs($owner)
            ->delete(route('properties.destroy', $property))
            ->assertRedirect(route('properties.my'));

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
        Storage::disk('public')->assertMissing($remainingPath);
    }

    public function test_owner_cannot_edit_or_delete_another_owners_property(): void
    {
        $ownerA = $this->createUser('owner');
        $ownerB = $this->createUser('owner');

        $propertyA = Property::create([
            'owner_id' => $ownerA->id,
            'title' => 'Owner A Property in Ikoyi',
            'description' => 'Luxury 5 bedroom detached house in Old Ikoyi, Lagos.',
            'property_type' => 'house',
            'transaction_type' => 'sale',
            'price' => 500000000,
            'location' => 'Ikoyi, Lagos',
            'bedrooms' => 5,
            'bathrooms' => 6,
            'status' => 'available',
        ]);

        $this->actingAs($ownerB)
            ->get(route('properties.edit', $propertyA))
            ->assertForbidden();

        $this->actingAs($ownerB)
            ->put(route('properties.update', $propertyA), [
                'title' => 'Hijacked Title',
                'description' => 'Attempting to modify another owner property.',
                'property_type' => 'house',
                'transaction_type' => 'sale',
                'price' => 10000,
                'location' => 'Ikoyi, Lagos',
                'status' => 'sold',
            ])
            ->assertForbidden();

        $this->actingAs($ownerB)
            ->delete(route('properties.destroy', $propertyA))
            ->assertForbidden();
    }

    public function test_admin_can_manage_all_properties(): void
    {
        $admin = $this->createUser('admin');
        $owner = $this->createUser('owner');

        $property = Property::create([
            'owner_id' => $owner->id,
            'title' => 'Maitama Luxury Residence',
            'description' => '6 bedroom mansion in Maitama Abuja with C of O.',
            'property_type' => 'house',
            'transaction_type' => 'sale',
            'price' => 600000000,
            'location' => 'Maitama, Abuja',
            'bedrooms' => 6,
            'bathrooms' => 7,
            'status' => 'available',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.properties.index'))
            ->assertOk()
            ->assertSee('Maitama Luxury Residence');

        $this->actingAs($admin)
            ->patch(route('properties.update-status', $property), ['status' => 'sold'])
            ->assertRedirect();

        $this->assertEquals('sold', $property->fresh()->status);

        $this->actingAs($admin)
            ->delete(route('properties.destroy', $property))
            ->assertRedirect();

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }
}
