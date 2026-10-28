<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Content-Based AI Property Recommendation Engine for NaijaProps.
 *
 * Ranks available properties using four core content attributes specified in the PRD:
 * 1. Location similarity (up to 35 points)
 * 2. Property type match (up to 25 points)
 * 3. Price range compatibility (up to 25 points)
 * 4. Number of bedrooms similarity (up to 15 points)
 *
 * Total possible score = 100 points. Every recommended listing includes
 * human-readable "Why suggested" explanations.
 */
class PropertyRecommendationService
{
    public const WEIGHT_LOCATION = 35;
    public const WEIGHT_PROPERTY_TYPE = 25;
    public const WEIGHT_PRICE_RANGE = 25;
    public const WEIGHT_BEDROOMS = 15;

    /**
     * Related residential property types for partial similarity scoring.
     *
     * @var array<string, list<string>>
     */
    private const RELATED_TYPES = [
        'duplex' => ['terrace', 'house', 'bungalow'],
        'terrace' => ['duplex', 'house', 'apartment'],
        'house' => ['duplex', 'terrace', 'bungalow'],
        'bungalow' => ['house', 'duplex', 'apartment'],
        'apartment' => ['terrace', 'bungalow'],
        'land' => ['commercial'],
        'commercial' => ['land', 'house'],
    ];

    /**
     * Recommend ranked properties for a user or guest, combining explicit filter preferences,
     * saved properties, past enquiries, and recently viewed listings.
     *
     * @param  array<string, mixed>  $explicitPreferences
     * @param  list<int>  $recentlyViewedIds
     * @return array{
     *     recommendations: Collection<int, array<string, mixed>>,
     *     profile: array<string, mixed>
     * }
     */
    public function getRecommendations(
        ?User $user = null,
        array $explicitPreferences = [],
        array $recentlyViewedIds = [],
        int $limit = 6
    ): array {
        $profile = $this->buildPreferenceProfile($user, $explicitPreferences, $recentlyViewedIds);

        $candidates = Property::query()
            ->with(['primaryImage', 'owner'])
            ->withCount('images')
            ->where('status', 'available')
            ->when(! empty($profile['exclude_property_ids']), function ($query) use ($profile) {
                $query->whereNotIn('id', $profile['exclude_property_ids']);
            })
            ->get();

        // If excluding saved/owned properties leaves too few candidates, include all available properties
        // except the user's own listings.
        if ($candidates->count() < min(3, $limit)) {
            $candidates = Property::query()
                ->with(['primaryImage', 'owner'])
                ->withCount('images')
                ->where('status', 'available')
                ->when($user, fn ($q) => $q->where('owner_id', '!=', $user->id))
                ->get();
        }

        $scored = $candidates
            ->map(fn (Property $property) => $this->scorePropertyAgainstProfile($property, $profile))
            ->sort(function (array $a, array $b) {
                if ($b['score'] !== $a['score']) {
                    return $b['score'] <=> $a['score'];
                }

                // Tie-breaker: newer listings first
                return ($b['property']->created_at?->timestamp ?? 0) <=> ($a['property']->created_at?->timestamp ?? 0);
            })
            ->values()
            ->take($limit)
            ->map(function (array $item, int $index) {
                $item['rank'] = $index + 1;

                return $item;
            });

        return [
            'recommendations' => $scored,
            'profile' => $profile,
        ];
    }

    /**
     * Recommend ranked similar properties for a single reference property on its details page.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getSimilarProperties(Property $reference, int $limit = 3): Collection
    {
        $referenceCity = $this->extractCity($reference->location);

        $profile = [
            'source' => 'similar_listing',
            'source_label' => 'Similar to ' . $reference->title,
            'locations' => [$reference->location],
            'cities' => array_values(array_filter([$referenceCity])),
            'property_types' => [$reference->property_type],
            'transaction_type' => $reference->transaction_type,
            'target_price' => (float) $reference->price,
            'min_price' => (float) $reference->price * 0.65,
            'max_price' => (float) $reference->price * 1.35,
            'price_range_key' => $this->resolvePriceRangeKey((float) $reference->price),
            'bedrooms' => $reference->bedrooms,
            'exclude_property_ids' => [$reference->id],
        ];

        $candidates = Property::query()
            ->with(['primaryImage', 'owner'])
            ->withCount('images')
            ->where('status', 'available')
            ->where('id', '!=', $reference->id)
            ->get();

        return $candidates
            ->map(fn (Property $candidate) => $this->scorePropertyAgainstProfile($candidate, $profile, $reference))
            ->filter(fn (array $item) => $item['score'] >= 20)
            ->sort(function (array $a, array $b) {
                if ($b['score'] !== $a['score']) {
                    return $b['score'] <=> $a['score'];
                }

                return ($b['property']->created_at?->timestamp ?? 0) <=> ($a['property']->created_at?->timestamp ?? 0);
            })
            ->values()
            ->take($limit)
            ->map(function (array $item, int $index) {
                $item['rank'] = $index + 1;

                return $item;
            });
    }

    /**
     * Build an explainable content preference profile from explicit inputs, user history, or smart defaults.
     *
     * @param  array<string, mixed>  $explicit
     * @param  list<int>  $recentlyViewedIds
     * @return array<string, mixed>
     */
    public function buildPreferenceProfile(?User $user, array $explicit = [], array $recentlyViewedIds = []): array
    {
        $hasExplicit = ! empty(array_filter([
            $explicit['location'] ?? null,
            $explicit['property_type'] ?? null,
            $explicit['transaction_type'] ?? null,
            $explicit['price_range'] ?? null,
            $explicit['min_price'] ?? null,
            $explicit['max_price'] ?? null,
            $explicit['bedrooms'] ?? null,
        ], fn ($v) => $v !== null && $v !== ''));

        $excludeIds = [];
        if ($user) {
            // Do not recommend a property to the owner/agent who listed it
            $excludeIds = $user->properties()->pluck('id')->all();
        }

        // 1. If the user explicitly supplied filter preferences, prioritize those
        if ($hasExplicit) {
            $minPrice = isset($explicit['min_price']) && is_numeric($explicit['min_price']) ? (float) $explicit['min_price'] : null;
            $maxPrice = isset($explicit['max_price']) && is_numeric($explicit['max_price']) ? (float) $explicit['max_price'] : null;
            $priceRangeKey = ! empty($explicit['price_range']) && isset(Property::PRICE_RANGES[$explicit['price_range']])
                ? (string) $explicit['price_range']
                : null;

            if ($priceRangeKey !== null) {
                $preset = Property::PRICE_RANGES[$priceRangeKey];
                $minPrice ??= $preset['min'] !== null ? (float) $preset['min'] : null;
                $maxPrice ??= $preset['max'] !== null ? (float) $preset['max'] : null;
            }

            if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
                [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
            }

            $targetPrice = null;
            if ($minPrice !== null && $maxPrice !== null) {
                $targetPrice = ($minPrice + $maxPrice) / 2;
            } elseif ($maxPrice !== null) {
                $targetPrice = $maxPrice * 0.75;
            } elseif ($minPrice !== null) {
                $targetPrice = $minPrice * 1.25;
            }

            $location = ! empty($explicit['location']) ? trim((string) $explicit['location']) : null;
            $city = $location ? $this->extractCity($location) : null;

            return [
                'source' => 'explicit_preferences',
                'source_label' => 'Your Selected Preferences',
                'locations' => $location ? [$location] : [],
                'cities' => $city ? [$city] : [],
                'property_types' => ! empty($explicit['property_type']) ? [(string) $explicit['property_type']] : [],
                'transaction_type' => ! empty($explicit['transaction_type']) ? (string) $explicit['transaction_type'] : null,
                'target_price' => $targetPrice,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'price_range_key' => $priceRangeKey,
                'bedrooms' => isset($explicit['bedrooms']) && is_numeric($explicit['bedrooms']) ? (int) $explicit['bedrooms'] : null,
                'exclude_property_ids' => $excludeIds,
            ];
        }

        // 2. Otherwise, infer preferences from the user's saved properties, enquiries, and recently viewed properties
        $interactedProperties = collect();

        if ($user) {
            $savedProps = Property::query()
                ->whereIn('id', $user->savedProperties()->pluck('property_id'))
                ->get();

            $enquiredProps = Property::query()
                ->whereIn('id', $user->enquiries()->pluck('property_id'))
                ->get();

            $interactedProperties = $interactedProperties
                ->concat($savedProps)
                ->concat($enquiredProps);
        }

        if (! empty($recentlyViewedIds)) {
            $viewedProps = Property::query()
                ->whereIn('id', array_slice($recentlyViewedIds, 0, 8))
                ->get();

            $interactedProperties = $interactedProperties->concat($viewedProps);
        }

        $interactedProperties = $interactedProperties->unique('id')->values();

        if ($interactedProperties->isNotEmpty()) {
            $locations = $interactedProperties
                ->pluck('location')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(3)
                ->values()
                ->all();

            $cities = $interactedProperties
                ->map(fn (Property $p) => $this->extractCity($p->location))
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(2)
                ->values()
                ->all();

            $propertyTypes = $interactedProperties
                ->pluck('property_type')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->take(2)
                ->values()
                ->all();

            $dominantTransactionType = $interactedProperties
                ->pluck('transaction_type')
                ->filter()
                ->countBy()
                ->sortDesc()
                ->keys()
                ->first();

            // Compute average price within the dominant transaction type so sale & rent prices don't skew each other
            $priceSample = $interactedProperties
                ->where('transaction_type', $dominantTransactionType)
                ->pluck('price')
                ->map(fn ($p) => (float) $p);

            if ($priceSample->isEmpty()) {
                $priceSample = $interactedProperties->pluck('price')->map(fn ($p) => (float) $p);
            }

            $avgPrice = $priceSample->avg();
            $minPrice = $avgPrice ? round($avgPrice * 0.55, 2) : null;
            $maxPrice = $avgPrice ? round($avgPrice * 1.45, 2) : null;

            $bedroomSample = $interactedProperties
                ->pluck('bedrooms')
                ->filter(fn ($b) => $b !== null);

            $typicalBedrooms = $bedroomSample->isNotEmpty()
                ? (int) round((float) $bedroomSample->avg())
                : null;

            return [
                'source' => 'user_activity',
                'source_label' => $user
                    ? 'Based on Your Saved Properties & Enquiries'
                    : 'Based on Your Recently Viewed Properties',
                'locations' => $locations,
                'cities' => $cities,
                'property_types' => $propertyTypes,
                'transaction_type' => $dominantTransactionType,
                'target_price' => $avgPrice ? round((float) $avgPrice, 2) : null,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'price_range_key' => $avgPrice ? $this->resolvePriceRangeKey((float) $avgPrice) : null,
                'bedrooms' => $typicalBedrooms,
                'exclude_property_ids' => $excludeIds,
            ];
        }

        // 3. Default Nigerian Market Baseline Profile (for new visitors / users with no activity yet)
        return [
            'source' => 'market_highlights',
            'source_label' => 'Popular Nigerian Market Preferences (Lagos, Abuja & Port Harcourt)',
            'locations' => ['Lekki Phase 1, Lagos', 'Victoria Island, Lagos', 'Guzape, Abuja', 'Old GRA, Port Harcourt'],
            'cities' => ['Lagos', 'Abuja', 'Port Harcourt'],
            'property_types' => ['duplex', 'apartment', 'house'],
            'transaction_type' => null,
            'target_price' => 85000000.0,
            'min_price' => 3000000.0,
            'max_price' => 350000000.0,
            'price_range_key' => null,
            'bedrooms' => 4,
            'exclude_property_ids' => $excludeIds,
        ];
    }

    /**
     * Score a single property against the preference profile and generate human-readable "Why suggested" reasons.
     *
     * @param  array<string, mixed>  $profile
     * @return array{
     *     property: Property,
     *     score: int,
     *     rank: int,
     *     match_label: string,
     *     reasons: list<string>,
     *     why_suggested: string,
     *     breakdown: array<string, int>
     * }
     */
    public function scorePropertyAgainstProfile(Property $property, array $profile, ?Property $referenceProperty = null): array
    {
        $reasons = [];
        $locationScore = 0;
        $typeScore = 0;
        $priceScore = 0;
        $bedroomScore = 0;

        $propertyCity = $this->extractCity($property->location);
        $preferredLocations = $profile['locations'] ?? [];
        $preferredCities = $profile['cities'] ?? [];

        // ------------------------------------------------------------------
        // 1. LOCATION SIMILARITY (Max 35 points)
        // ------------------------------------------------------------------
        $exactLocationMatch = false;
        foreach ($preferredLocations as $prefLoc) {
            if (
                strcasecmp(trim($property->location), trim((string) $prefLoc)) === 0
                || stripos($property->location, (string) $prefLoc) !== false
            ) {
                $exactLocationMatch = true;
                break;
            }
        }

        if ($exactLocationMatch) {
            $locationScore = self::WEIGHT_LOCATION; // 35 pts
            if ($referenceProperty) {
                $reasons[] = "Same neighbourhood ({$property->location})";
            } elseif (($profile['source'] ?? '') === 'explicit_preferences') {
                $reasons[] = "Matches your selected location ({$property->location})";
            } elseif (($profile['source'] ?? '') === 'user_activity') {
                $reasons[] = "Located in {$property->location} (matches your saved/enquired area)";
            } else {
                $reasons[] = "Prime neighbourhood in {$property->location}";
            }
        } elseif ($propertyCity && in_array($propertyCity, $preferredCities, true)) {
            $cityIndex = array_search($propertyCity, $preferredCities, true);
            $locationScore = $cityIndex === 0 ? 26 : 20;
            if ($referenceProperty) {
                $reasons[] = "Also located in {$propertyCity} ({$property->location})";
            } elseif (($profile['source'] ?? '') === 'user_activity') {
                $reasons[] = "Located in {$propertyCity}, a city from your saved listings";
            } else {
                $reasons[] = "High-demand location in {$propertyCity}";
            }
        }

        // ------------------------------------------------------------------
        // 2. PROPERTY TYPE MATCH (Max 25 points)
        // ------------------------------------------------------------------
        $preferredTypes = $profile['property_types'] ?? [];
        $typeLabel = $property->property_type_label;

        if (! empty($preferredTypes) && in_array($property->property_type, $preferredTypes, true)) {
            $typeIndex = array_search($property->property_type, $preferredTypes, true);
            $typeScore = $typeIndex === 0 ? self::WEIGHT_PROPERTY_TYPE : 20;

            if ($referenceProperty) {
                $reasons[] = "Same property type ({$typeLabel})";
            } elseif (($profile['source'] ?? '') === 'explicit_preferences') {
                $reasons[] = "Exact match for {$typeLabel}";
            } elseif (($profile['source'] ?? '') === 'user_activity') {
                $reasons[] = "Matches your interest in {$typeLabel} listings";
            } else {
                $reasons[] = "Popular {$typeLabel} category";
            }
        } elseif (! empty($preferredTypes)) {
            $primaryPreferredType = $preferredTypes[0];
            $related = self::RELATED_TYPES[$primaryPreferredType] ?? [];
            if (in_array($property->property_type, $related, true)) {
                $typeScore = 13;
                $reasons[] = "Comparable residential style ({$typeLabel})";
            }
        } else {
            // No specific type filter applied — award baseline points
            $typeScore = 15;
        }

        // ------------------------------------------------------------------
        // 3. PRICE RANGE COMPATIBILITY (Max 25 points)
        // ------------------------------------------------------------------
        $price = (float) $property->price;
        $minPrice = $profile['min_price'] ?? null;
        $maxPrice = $profile['max_price'] ?? null;
        $targetPrice = $profile['target_price'] ?? null;
        $preferredTx = $profile['transaction_type'] ?? null;
        $priceRangeKey = $profile['price_range_key'] ?? null;

        $txMatches = $preferredTx === null || $preferredTx === $property->transaction_type;

        if ($minPrice !== null || $maxPrice !== null) {
            $withinMin = $minPrice === null || $price >= $minPrice;
            $withinMax = $maxPrice === null || $price <= $maxPrice;

            if ($withinMin && $withinMax && $txMatches) {
                $priceScore = self::WEIGHT_PRICE_RANGE; // 25 pts

                if ($priceRangeKey && isset(Property::PRICE_RANGES[$priceRangeKey])) {
                    $rangeLabel = Property::PRICE_RANGES[$priceRangeKey]['label'];
                    $reasons[] = "Within {$rangeLabel} price range ({$property->formatted_price})";
                } elseif ($referenceProperty) {
                    $reasons[] = "Comparable price point ({$property->formatted_price})";
                } else {
                    $reasons[] = "Fits your target Naira budget ({$property->formatted_price})";
                }
            } elseif ($targetPrice !== null && $targetPrice > 0 && $txMatches) {
                $diffRatio = abs($price - $targetPrice) / $targetPrice;
                if ($diffRatio <= 0.35) {
                    $priceScore = 18;
                    $reasons[] = "Close to your target price range ({$property->formatted_price})";
                } elseif ($diffRatio <= 0.60) {
                    $priceScore = 11;
                    $reasons[] = "Within reachable price bracket ({$property->formatted_price})";
                } elseif ($maxPrice !== null && $price < $minPrice) {
                    $priceScore = 14;
                    $reasons[] = "Budget-friendly option at {$property->formatted_price}";
                }
            } elseif ($maxPrice !== null && $price <= $maxPrice * 1.15) {
                $priceScore = 12;
                $reasons[] = "Competitive market price ({$property->formatted_price})";
            }
        } else {
            $priceScore = 15;
            $reasons[] = "Competitive market price ({$property->formatted_price})";
        }

        // ------------------------------------------------------------------
        // 4. BEDROOMS SIMILARITY (Max 15 points)
        // ------------------------------------------------------------------
        $preferredBedrooms = $profile['bedrooms'] ?? null;

        if ($property->property_type === 'land' && in_array('land', $preferredTypes, true)) {
            $bedroomScore = self::WEIGHT_BEDROOMS;
            $reasons[] = 'Verified land/plot specification';
        } elseif ($preferredBedrooms !== null && $property->bedrooms !== null) {
            $bedDiff = abs($property->bedrooms - (int) $preferredBedrooms);

            if ($bedDiff === 0) {
                $bedroomScore = self::WEIGHT_BEDROOMS; // 15 pts
                $reasons[] = "Exact {$property->bedrooms}-bedroom layout match";
            } elseif ($bedDiff === 1) {
                $bedroomScore = 10;
                $reasons[] = "Similar size ({$property->bedrooms} bedrooms)";
            } elseif ($property->bedrooms >= (int) $preferredBedrooms && ($profile['source'] ?? '') === 'explicit_preferences') {
                $bedroomScore = 12;
                $reasons[] = "Meets {$preferredBedrooms}+ bedrooms requirement ({$property->bedrooms} beds)";
            } elseif ($bedDiff === 2) {
                $bedroomScore = 5;
            }
        } elseif ($preferredBedrooms === null && $property->bedrooms !== null) {
            $bedroomScore = 10;
            $reasons[] = "Spacious {$property->bedrooms}-bedroom configuration";
        }

        // Ensure at least two clear human-readable reasons are always present
        if (count($reasons) < 2) {
            $txLabel = $property->transaction_type === 'sale' ? 'For Sale' : 'For Rent';
            $reasons[] = "Verified {$txLabel} listing in {$property->location}";
        }

        $totalScore = min(100, max(15, $locationScore + $typeScore + $priceScore + $bedroomScore));

        $matchLabel = match (true) {
            $totalScore >= 85 => 'Excellent Match',
            $totalScore >= 70 => 'Strong Match',
            $totalScore >= 55 => 'Good Match',
            default => 'Recommended Pick',
        };

        $topReasons = array_slice(array_values(array_unique($reasons)), 0, 4);

        return [
            'property' => $property,
            'score' => $totalScore,
            'rank' => 0,
            'match_label' => $matchLabel,
            'reasons' => $topReasons,
            'why_suggested' => implode(' • ', array_slice($topReasons, 0, 3)),
            'breakdown' => [
                'location' => $locationScore,
                'property_type' => $typeScore,
                'price_range' => $priceScore,
                'bedrooms' => $bedroomScore,
            ],
        ];
    }

    /**
     * Extract the major Nigerian city (Lagos, Abuja, Port Harcourt, etc.) from a location string.
     */
    public function extractCity(?string $location): ?string
    {
        if (! $location) {
            return null;
        }

        foreach (['Lagos', 'Abuja', 'Port Harcourt'] as $majorCity) {
            if (stripos($location, $majorCity) !== false) {
                return $majorCity;
            }
        }

        $parts = array_filter(array_map('trim', explode(',', $location)));

        return ! empty($parts) ? (string) end($parts) : trim($location);
    }

    /**
     * Determine which preset price_range key a Naira amount falls into.
     */
    public function resolvePriceRangeKey(float $price): ?string
    {
        foreach (Property::PRICE_RANGES as $key => $range) {
            $minOk = $range['min'] === null || $price >= (float) $range['min'];
            $maxOk = $range['max'] === null || $price <= (float) $range['max'];
            if ($minOk && $maxOk) {
                return $key;
            }
        }

        return null;
    }
}
