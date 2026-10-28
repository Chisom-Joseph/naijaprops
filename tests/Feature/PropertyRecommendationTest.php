<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\Property;
use App\Models\SavedProperty;
use App\Models\User;
use App\Services\PropertyRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyRecommendationTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $buyer;

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

        $this->buyer = User::create([
            'full_name' => 'Amaka Eze',
            'email' => 'buyer@naijaprops.ng',
            'password' => 'password',
            'phone' => '+234 703 555 1212',
            'role' => 'buyer',
            'status' => 'active',
        ]);
    }

    private function seedProperties(): array
    {
        $lekkiDuplex = Property::create([
            'owner_id' => $this->owner->id,
            'title' => '4 Bedroom Contemporary Smart Duplex in Lekki Phase 1',
            'description' => 'Luxury 4-bedroom duplex in Lekki Phase 1, Lagos.',
            'property_type' => 'duplex',
            'transaction_type' => 'sale',
            'price' => 250000000,
            'location' => 'Lekki Phase 1, Lagos',
            'bedrooms' => 4,
            'bathrooms' => 5,
            'status' => 'available',
        ]);

        $ikoyiDuplex = Property::create([
            'owner_id' => $this->owner->id,
            'title' => '4 Bedroom Semi-Detached Duplex in Ikoyi',
            'description' => 'Contemporary 4-bedroom duplex in Old Ikoyi, Lagos.',
            'property_type' => 'duplex',
            'transaction_type' => 'sale',
            'price' => 280000000,
            'location' => 'Ikoyi, Lagos',
            'bedrooms' => 4,
            'bathrooms' => 5,
            'status' => 'available',
        ]);

        $phFlat = Property::create([
            'owner_id' => $this->owner->id,
            'title' => '2 Bedroom Flat on Peter Odili Road',
            'description' => 'Spacious 2-bedroom apartment in Port Harcourt.',
            'property_type' => 'apartment',
            'transaction_type' => 'rent',
            'price' => 3500000,
            'location' => 'Peter Odili Road, Port Harcourt',
            'bedrooms' => 2,
            'bathrooms' => 3,
            'status' => 'available',
        ]);

        $abujaLand = Property::create([
            'owner_id' => $this->owner->id,
            'title' => '1000 SQM Residential Plot in Katampe Extension',
            'description' => 'Dry table land in Abuja.',
            'property_type' => 'land',
            'transaction_type' => 'sale',
            'price' => 95000000,
            'location' => 'Katampe Extension, Abuja',
            'bedrooms' => null,
            'bathrooms' => null,
            'status' => 'available',
        ]);

        return compact('lekkiDuplex', 'ikoyiDuplex', 'phFlat', 'abujaLand');
    }

    public function test_recommendation_service_ranks_properties_by_location_type_price_and_bedrooms_with_why_suggested_reasons(): void
    {
        $props = $this->seedProperties();

        /** @var PropertyRecommendationService $service */
        $service = app(PropertyRecommendationService::class);

        $result = $service->getRecommendations(
            user: $this->buyer,
            explicitPreferences: [
                'location' => 'Lekki Phase 1, Lagos',
                'property_type' => 'duplex',
                'transaction_type' => 'sale',
                'price_range' => '100m_300m',
                'bedrooms' => 4,
            ],
            limit: 4
        );

        $recommendations = $result['recommendations'];

        $this->assertNotEmpty($recommendations);

        // #1 ranked property should be the exact match in Lekki Phase 1, Lagos (4-bed duplex within ₦100M–₦300M)
        $first = $recommendations->first();
        $this->assertSame(1, $first['rank']);
        $this->assertSame($props['lekkiDuplex']->id, $first['property']->id);
        $this->assertSame(100, $first['score']);
        $this->assertNotEmpty($first['reasons']);
        $this->assertStringContainsString('Lekki Phase 1, Lagos', $first['why_suggested']);
        $this->assertStringContainsString('Duplex', $first['why_suggested']);

        // #2 ranked property should be the Ikoyi, Lagos 4-bedroom duplex (same city, same type, same price range, same bedrooms)
        $second = $recommendations->get(1);
        $this->assertSame(2, $second['rank']);
        $this->assertSame($props['ikoyiDuplex']->id, $second['property']->id);
        $this->assertGreaterThan($recommendations->last()['score'], $second['score']);
    }

    public function test_recommendation_service_infers_profile_from_saved_properties_and_enquiries(): void
    {
        $props = $this->seedProperties();

        // Buyer saves the Lekki Duplex and sends an enquiry on it
        SavedProperty::create([
            'user_id' => $this->buyer->id,
            'property_id' => $props['lekkiDuplex']->id,
        ]);

        Enquiry::create([
            'user_id' => $this->buyer->id,
            'property_id' => $props['lekkiDuplex']->id,
            'message' => 'I am interested in inspecting this 4 bedroom duplex in Lagos.',
            'status' => 'pending',
        ]);

        /** @var PropertyRecommendationService $service */
        $service = app(PropertyRecommendationService::class);

        $result = $service->getRecommendations(user: $this->buyer, limit: 4);

        $this->assertSame('user_activity', $result['profile']['source']);
        $this->assertContains('Lagos', $result['profile']['cities']);
        $this->assertContains('duplex', $result['profile']['property_types']);

        // Top 2 recommendations should be the two Lagos 4-bedroom duplexes
        $topTwoIds = $result['recommendations']->take(2)->pluck('property.id')->all();
        $this->assertContains($props['lekkiDuplex']->id, $topTwoIds);
        $this->assertContains($props['ikoyiDuplex']->id, $topTwoIds);
    }

    public function test_recommendations_page_displays_ranked_cards_and_why_suggested_explanations(): void
    {
        $props = $this->seedProperties();

        $response = $this->get(route('recommendations.index', [
            'location' => 'Peter Odili Road, Port Harcourt',
            'property_type' => 'apartment',
            'transaction_type' => 'rent',
            'bedrooms' => 2,
        ]));

        $response->assertOk();
        $response->assertSee('AI-Assisted Property Recommendations');
        $response->assertSee('Why suggested');
        $response->assertSee('#1 Pick');
        $response->assertSee($props['phFlat']->title);
        $response->assertSee('Peter Odili Road, Port Harcourt');
    }

    public function test_home_page_and_property_details_page_render_why_suggested_recommendations(): void
    {
        $props = $this->seedProperties();

        // Home page renders AI-Assisted Recommendations section
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertOk();
        $homeResponse->assertSee('AI-Assisted Recommendations');
        $homeResponse->assertSee('Why suggested');

        // Property details page renders Similar Properties with AI content ranking and "Why suggested"
        $showResponse = $this->get(route('properties.show', $props['lekkiDuplex']));
        $showResponse->assertOk();
        $showResponse->assertSee('Similar Properties Recommended For You');
        $showResponse->assertSee('Why suggested');
        $showResponse->assertSee($props['ikoyiDuplex']->title);
    }
}
