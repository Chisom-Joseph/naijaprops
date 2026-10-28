<?php

namespace App\Http\Requests\Property;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;

class RecommendationRequest extends FormRequest
{
    /**
     * Anyone (guest or authenticated user) can view and customize AI recommendations.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize query parameters before validation so invalid query strings degrade gracefully.
     */
    protected function prepareForValidation(): void
    {
        $propertyType = $this->input('property_type');
        if ($propertyType !== null && ! array_key_exists((string) $propertyType, Property::PROPERTY_TYPES)) {
            $propertyType = null;
        }

        $transactionType = $this->input('transaction_type');
        if ($transactionType !== null && ! array_key_exists((string) $transactionType, Property::TRANSACTION_TYPES)) {
            $transactionType = null;
        }

        $priceRange = $this->input('price_range');
        if ($priceRange !== null && ! array_key_exists((string) $priceRange, Property::PRICE_RANGES)) {
            $priceRange = null;
        }

        $bedrooms = $this->input('bedrooms');
        if ($bedrooms !== null && (! is_numeric($bedrooms) || (int) $bedrooms < 1 || (int) $bedrooms > 20)) {
            $bedrooms = null;
        }

        $minPrice = $this->input('min_price');
        if ($minPrice !== null && (! is_numeric($minPrice) || (float) $minPrice < 0)) {
            $minPrice = null;
        }

        $maxPrice = $this->input('max_price');
        if ($maxPrice !== null && (! is_numeric($maxPrice) || (float) $maxPrice < 0)) {
            $maxPrice = null;
        }

        $this->merge([
            'location' => is_string($this->input('location')) ? trim($this->input('location')) : null,
            'property_type' => $propertyType,
            'transaction_type' => $transactionType,
            'price_range' => $priceRange,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'bedrooms' => $bedrooms !== null ? (int) $bedrooms : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the recommendation request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'location' => ['nullable', 'string', 'max:120'],
            'property_type' => ['nullable', 'string'],
            'transaction_type' => ['nullable', 'string'],
            'price_range' => ['nullable', 'string'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:1', 'max:20'],
        ];
    }

    /**
     * Return validated preference parameters for PropertyRecommendationService.
     *
     * @return array<string, mixed>
     */
    public function preferences(): array
    {
        $validated = $this->validated();

        return [
            'location' => $validated['location'] ?? null,
            'property_type' => $validated['property_type'] ?? null,
            'transaction_type' => $validated['transaction_type'] ?? null,
            'price_range' => $validated['price_range'] ?? null,
            'min_price' => isset($validated['min_price']) && $validated['min_price'] !== '' ? (float) $validated['min_price'] : null,
            'max_price' => isset($validated['max_price']) && $validated['max_price'] !== '' ? (float) $validated['max_price'] : null,
            'bedrooms' => isset($validated['bedrooms']) && $validated['bedrooms'] !== '' ? (int) $validated['bedrooms'] : null,
        ];
    }
}
