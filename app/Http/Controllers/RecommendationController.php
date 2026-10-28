<?php

namespace App\Http\Controllers;

use App\Http\Requests\Property\RecommendationRequest;
use App\Services\PropertyRecommendationService;
use Illuminate\View\View;

class RecommendationController extends Controller
{
    public function __construct(
        private readonly PropertyRecommendationService $recommendationService
    ) {}

    /**
     * Display the AI-Assisted Property Recommendations page with content-based ranking
     * and "Why suggested" explanations.
     */
    public function index(RecommendationRequest $request): View
    {
        $preferences = $request->preferences();

        // If no explicit query parameters were passed, fall back to any stored session search preferences
        $hasExplicitPreferences = ! empty(array_filter($preferences, fn ($v) => $v !== null && $v !== ''));
        if ($hasExplicitPreferences) {
            $request->session()->put('ai_preferences', $preferences);
        }

        $recentlyViewedIds = (array) $request->session()->get('recently_viewed_properties', []);

        $result = $this->recommendationService->getRecommendations(
            user: $request->user(),
            explicitPreferences: $preferences,
            recentlyViewedIds: $recentlyViewedIds,
            limit: 9
        );

        $savedPropertyIds = $request->user()
            ? $request->user()->savedProperties()->pluck('property_id')->all()
            : [];

        return view('recommendations.index', [
            'recommendations' => $result['recommendations'],
            'profile' => $result['profile'],
            'preferences' => $preferences,
            'savedPropertyIds' => $savedPropertyIds,
        ]);
    }
}
