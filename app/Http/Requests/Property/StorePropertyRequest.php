<?php

namespace App\Http\Requests\Property;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('create', Property::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'property_type' => ['required', 'string', Rule::in(array_keys(Property::PROPERTY_TYPES))],
            'transaction_type' => ['required', 'string', Rule::in(['sale', 'rent'])],
            'price' => ['required', 'numeric', 'min:1000', 'max:999999999999.99'],
            'location' => ['required', 'string', 'min:3', 'max:255'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'bathrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'status' => ['nullable', 'string', Rule::in(['available', 'pending', 'sold', 'rented'])],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'images.max' => 'You may upload a maximum of 10 images per property listing.',
            'images.*.image' => 'Each uploaded file must be a valid image.',
            'images.*.mimes' => 'Property images must be in JPEG, PNG, JPG, or WEBP format.',
            'images.*.max' => 'Each property image must not exceed 2MB (2048 KB).',
            'price.min' => 'Please enter a valid price in Nigerian Naira (₦).',
        ];
    }
}
