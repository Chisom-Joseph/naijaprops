<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertySearchFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'full_name' => 'Chief Adebayo Ogunlesi',
            'email' => 'owner@naijaprops.ng',
            'password' => 'password',
            'phone' => '+234 802 345 6789',
            'role' => 'owner',
            'status' => 'active',
        ]);
    }

    private function seedSampleProperties(): void
    {
        Property::create([
            'owner_id' => $this->owner->id,
            'title' => '5 Bedroom Fully Detached Duplex in Lekki Phase 1',
            'description' => 'Luxury detached duplex with private swimming pool and BQ in Lekki.',
            'property_type' => 'duplex',
            'transaction_type' => 'sale',
            'price' => 320000000,
            'location' => 'Lekki Phase 1, Lagos',
            'bedrooms' => 5,
            'bathrooms' => 6,
            'status' => 'available',
        ]);

        Property::create([
            'owner_id' => $this->owner->id,
            'title' => '3 Bedroom Serviced Apartment in Victoria Island',
            'description' => 'Modern ocean view apartment with 24/7 power and elevator.',
            'property_type' => 'apartment',
            'transaction_type' => 'rent',
            'price' => 18000000,
            'location' => 'Victoria Island, Lagos',
            'bedrooms' => 3,
            'bathrooms' => 4,
            'status' => 'available',
        ]);

        Property::create([
            'owner_id' => $this->owner->id,
            'title' => '6 Bedroom Ambassadorial Mansion in Maitama',
            'description' => 'Ultra-luxury detached house on 1200 sqm with C of O in Abuja.',
            'property_type' => 'house',
            'transaction_type' => 'sale',
            'price' => 650000000,
            'location' => 'Maitama, Abuja',
            'bedrooms' => 6,
            'bathrooms' => 7,
            'status' => 'available',
        ]);

        Property::create([
            'owner_id' => $this->owner->id,
            'title' => 'Modern 3 Bedroom Flat on Peter Odili Road',
            'description' => 'Spacious 3 bedroom flat in a gated close in Port Harcourt.',
            'property_type' => 'apartment',
            'transaction_type' => 'rent',
            'price' => 3500000,
            'location' => 'Peter Odili Road, Port Harcourt',
            'bedrooms' => 3,
            'bathrooms' => 4,
            'status' => 'available',
        ]);

        Property::create([
            'owner_id' => $this->owner->id,
            'title' => '1000 SQM FCDA Residential Plot in Katampe',
            'description' => 'Dry table land with FCDA Right of Occupancy in Abuja.',
            'property_type' => 'land',
            'transaction_type' => 'sale',
            'price' => 95000000,
            'location' => 'Katampe Extension, Abuja',
            'bedrooms' => null,
            'bathrooms' => null,
            'status' => 'available',
        ]);
    }

    public function test_keyword_search_filters_properties_and_displays_found_count(): void
    {
        $this->seedSampleProperties();

        $response = $this->get(route('properties.index', ['search' => 'swimming pool']));

        $response->assertOk()
            ->assertSee('1 property found')
            ->assertSee('5 Bedroom Fully Detached Duplex in Lekki Phase 1')
            ->assertDontSee('6 Bedroom Ambassadorial Mansion in Maitama');
    }

    public function test_filters_by_location_property_type_and_transaction_type(): void
    {
        $this->seedSampleProperties();

        // Filter by city: Lagos
        $this->get(route('properties.index', ['location' => 'Lagos']))
            ->assertOk()
            ->assertSee('2 properties found')
            ->assertSee('5 Bedroom Fully Detached Duplex in Lekki Phase 1')
            ->assertSee('3 Bedroom Serviced Apartment in Victoria Island')
            ->assertDontSee('6 Bedroom Ambassadorial Mansion in Maitama');

        // Filter by property_type = apartment & transaction_type = rent
        $this->get(route('properties.index', [
            'property_type' => 'apartment',
            'transaction_type' => 'rent',
        ]))
            ->assertOk()
            ->assertSee('2 properties found')
            ->assertSee('3 Bedroom Serviced Apartment in Victoria Island')
            ->assertSee('Modern 3 Bedroom Flat on Peter Odili Road')
            ->assertDontSee('5 Bedroom Fully Detached Duplex in Lekki Phase 1');
    }

    public function test_filters_by_preset_price_range_and_custom_min_max_price_and_bedrooms(): void
    {
        $this->seedSampleProperties();

        // Preset price_range: 5m_20m (₦5M - ₦20M)
        $this->get(route('properties.index', ['price_range' => '5m_20m']))
            ->assertOk()
            ->assertSee('1 property found')
            ->assertSee('3 Bedroom Serviced Apartment in Victoria Island')
            ->assertDontSee('Modern 3 Bedroom Flat on Peter Odili Road');

        // Custom min_price & max_price + bedrooms >= 5
        $this->get(route('properties.index', [
            'min_price' => 300000000,
            'max_price' => 700000000,
            'bedrooms' => 5,
        ]))
            ->assertOk()
            ->assertSee('2 properties found')
            ->assertSee('5 Bedroom Fully Detached Duplex in Lekki Phase 1')
            ->assertSee('6 Bedroom Ambassadorial Mansion in Maitama')
            ->assertDontSee('1000 SQM FCDA Residential Plot in Katampe');
    }

    public function test_pagination_preserves_query_parameters_and_displays_total_properties_found(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            Property::create([
                'owner_id' => $this->owner->id,
                'title' => "Lekki Duplex Listing #{$i}",
                'description' => 'Executive 4 bedroom duplex in Lekki Lagos.',
                'property_type' => 'duplex',
                'transaction_type' => 'sale',
                'price' => 150000000 + ($i * 1000000),
                'location' => 'Lekki Phase 1, Lagos',
                'bedrooms' => 4,
                'bathrooms' => 5,
                'status' => 'available',
            ]);
        }

        $response = $this->get(route('properties.index', [
            'location' => 'Lagos',
            'property_type' => 'duplex',
        ]));

        $response->assertOk()
            ->assertSee('12 properties found')
            ->assertSee('location=Lagos')
            ->assertSee('property_type=duplex');
    }
}
