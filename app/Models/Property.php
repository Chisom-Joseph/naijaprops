<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Property extends Model
{
    use HasFactory;

    public const PROPERTY_TYPES = [
        'duplex' => 'Duplex',
        'apartment' => 'Apartment / Flat',
        'house' => 'Detached House / Mansion',
        'terrace' => 'Terrace Duplex',
        'bungalow' => 'Bungalow',
        'land' => 'Land / Plot',
        'commercial' => 'Commercial Property',
    ];

    public const TRANSACTION_TYPES = [
        'sale' => 'For Sale',
        'rent' => 'For Rent',
    ];

    public const STATUSES = [
        'available' => 'Available',
        'pending' => 'Pending',
        'sold' => 'Sold',
        'rented' => 'Rented',
    ];

    /**
     * Popular Nigerian cities and neighbourhoods for location filtering.
     */
    public const LOCATIONS = [
        'Lagos' => 'Lagos (All Areas)',
        'Lekki Phase 1, Lagos' => 'Lekki Phase 1, Lagos',
        'Victoria Island, Lagos' => 'Victoria Island, Lagos',
        'Ikoyi, Lagos' => 'Ikoyi, Lagos',
        'Ikeja GRA, Lagos' => 'Ikeja GRA, Lagos',
        'Abuja' => 'Abuja (All Areas)',
        'Maitama, Abuja' => 'Maitama, Abuja',
        'Guzape, Abuja' => 'Guzape, Abuja',
        'Wuse 2, Abuja' => 'Wuse 2, Abuja',
        'Katampe Extension, Abuja' => 'Katampe Extension, Abuja',
        'Port Harcourt' => 'Port Harcourt (All Areas)',
        'Old GRA, Port Harcourt' => 'Old GRA, Port Harcourt',
        'Peter Odili Road, Port Harcourt' => 'Peter Odili Road, Port Harcourt',
        'Eliozu, Port Harcourt' => 'Eliozu, Port Harcourt',
        'Ada-George, Port Harcourt' => 'Ada-George, Port Harcourt',
    ];

    /**
     * Preset Nigerian Naira (₦) price ranges for quick filtering.
     */
    public const PRICE_RANGES = [
        'under_5m' => [
            'label' => 'Under ₦5,000,000',
            'min' => null,
            'max' => 5000000,
        ],
        '5m_20m' => [
            'label' => '₦5M – ₦20M',
            'min' => 5000000,
            'max' => 20000000,
        ],
        '20m_100m' => [
            'label' => '₦20M – ₦100M',
            'min' => 20000000,
            'max' => 100000000,
        ],
        '100m_300m' => [
            'label' => '₦100M – ₦300M',
            'min' => 100000000,
            'max' => 300000000,
        ],
        'above_300m' => [
            'label' => 'Above ₦300,000,000',
            'min' => 300000000,
            'max' => null,
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'property_type',
        'transaction_type',
        'price',
        'location',
        'bedrooms',
        'bathrooms',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
        ];
    }

    /**
     * The user (owner or agent) who listed the property.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Images associated with this property.
     */
    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class, 'property_id');
    }

    /**
     * Primary (first) image for the property card.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(PropertyImage::class, 'property_id')->oldestOfMany('id');
    }

    /**
     * Enquiries made on this property.
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'property_id');
    }

    /**
     * Saved property records referencing this property.
     */
    public function savedProperties(): HasMany
    {
        return $this->hasMany(SavedProperty::class, 'property_id');
    }

    /**
     * Reports filed against this property.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'property_id');
    }

    /**
     * Format price in Nigerian Naira (₦).
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₦' . number_format((float) $this->price, 0);
    }

    /**
     * Get the primary image URL or a fallback Nigerian property placeholder.
     */
    public function getPrimaryImageUrlAttribute(): string
    {
        if ($this->relationLoaded('primaryImage') && $this->primaryImage) {
            return $this->primaryImage->url;
        }

        if ($this->relationLoaded('images') && $this->images->isNotEmpty()) {
            return $this->images->first()->url;
        }

        $firstImage = $this->primaryImage()->first();

        return $firstImage
            ? $firstImage->url
            : 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80';
    }

    /**
     * Human-readable property type label.
     */
    public function getPropertyTypeLabelAttribute(): string
    {
        return self::PROPERTY_TYPES[$this->property_type] ?? ucfirst($this->property_type);
    }

    /**
     * Scope a query to filter and sort properties based on search & filter criteria.
     *
     * @param  Builder<Property>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Property>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        // 1. Keyword search across title, location, description, and property_type
        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('property_type', 'like', "%{$search}%");
            });
        }

        // 2. Location filter (matches city or specific Nigerian neighbourhood)
        if (! empty($filters['location'])) {
            $location = trim((string) $filters['location']);
            $query->where('location', 'like', "%{$location}%");
        }

        // 3. Property type filter
        if (! empty($filters['property_type']) && array_key_exists((string) $filters['property_type'], self::PROPERTY_TYPES)) {
            $query->where('property_type', $filters['property_type']);
        }

        // 4. Transaction type filter (sale / rent)
        if (! empty($filters['transaction_type']) && array_key_exists((string) $filters['transaction_type'], self::TRANSACTION_TYPES)) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        // 5. Status filter (available, pending, sold, rented)
        if (! empty($filters['status']) && array_key_exists((string) $filters['status'], self::STATUSES)) {
            $query->where('status', $filters['status']);
        }

        // 6. Bedrooms filter (minimum bedrooms)
        if (! empty($filters['bedrooms']) && is_numeric($filters['bedrooms'])) {
            $query->where('bedrooms', '>=', (int) $filters['bedrooms']);
        }

        // 7. Price range filter (preset bracket and/or custom min_price & max_price in ₦)
        $minPrice = isset($filters['min_price']) && is_numeric($filters['min_price']) ? (float) $filters['min_price'] : null;
        $maxPrice = isset($filters['max_price']) && is_numeric($filters['max_price']) ? (float) $filters['max_price'] : null;

        if (! empty($filters['price_range']) && isset(self::PRICE_RANGES[$filters['price_range']])) {
            $preset = self::PRICE_RANGES[$filters['price_range']];
            if ($minPrice === null && $preset['min'] !== null) {
                $minPrice = (float) $preset['min'];
            }
            if ($maxPrice === null && $preset['max'] !== null) {
                $maxPrice = (float) $preset['max'];
            }
        }

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        // 8. Sorting
        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'bedrooms_desc' => $query->orderByRaw('CASE WHEN bedrooms IS NULL THEN 1 ELSE 0 END, bedrooms DESC'),
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        return $query;
    }
}
