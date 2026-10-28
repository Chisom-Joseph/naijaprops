<?php

namespace App\Http\Requests\Property;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;

class SearchPropertyRequest extends FormRequest
{
    /**
     * Anyone (guest or authenticated user) can search and filter properties.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize and sanitize query parameters before validation so malformed
     * query strings degrade gracefully on GET requests.
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

        $status = $this->input('status');
        if ($status !== null && ! array_key_exists((string) $status, Property::STATUSES)) {
            $status = null;
        }

        $sort = $this->input('sort');
        if ($sort !== null && ! in_array((string) $sort, ['latest', 'oldest', 'price_asc', 'price_desc', 'bedrooms_desc'], true)) {
            $sort = 'latest';
        }

        $bedrooms = $this->input('bedrooms');
        if ($bedrooms !== null && (! is_numeric($bedrooms) || (int) $bedrooms < 0)) {
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
            'search' => is_string($this->input('search')) ? trim($this->input('search')) : null,
            'location' => is_string($this->input('location')) ? trim($this->input('location')) : null,
            'property_type' => $propertyType,
            'transaction_type' => $transactionType,
            'price_range' => $priceRange,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'bedrooms' => $bedrooms !== null ? (int) $bedrooms : null,
            'status' => $status,
            'sort' => $sort ?? 'latest',
        ]);
    }

    /**
     * Get the validation rules that apply to the search request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:120'],
            'property_type' => ['nullable', 'string'],
            'transaction_type' => ['nullable', 'string'],
            'price_range' => ['nullable', 'string'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:1', 'max:20'],
            'status' => ['nullable', 'string'],
            'sort' => ['nullable', 'string'],
        ];
    }

    /**
     * Return sanitized, validated filter criteria for the Property::filter() scope.
     *
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        $validated = $this->validated();

        return [
            'search' => $validated['search'] ?? null,
            'location' => $validated['location'] ?? null,
            'property_type' => $validated['property_type'] ?? null,
            'transaction_type' => $validated['transaction_type'] ?? null,
            'price_range' => $validated['price_range'] ?? null,
            'min_price' => isset($validated['min_price']) && $validated['min_price'] !== '' ? (float) $validated['min_price'] : null,
            'max_price' => isset($validated['max_price']) && $validated['max_price'] !== '' ? (float) $validated['max_price'] : null,
            'bedrooms' => isset($validated['bedrooms']) && $validated['bedrooms'] !== '' ? (int) $validated['bedrooms'] : null,
            'status' => $validated['status'] ?? null,
            'sort' => $validated['sort'] ?? 'latest',
        ];
    }
}
