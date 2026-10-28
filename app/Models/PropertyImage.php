<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped with standard created_at/updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'property_id',
        'image_path',
        'uploaded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
        ];
    }

    /**
     * The property this image belongs to.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * Resolve full public URL for both seeded external URLs and local storage uploads.
     */
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, '/storage/') || str_starts_with($this->image_path, 'storage/')) {
            return asset(ltrim($this->image_path, '/'));
        }

        return asset('storage/' . ltrim($this->image_path, '/'));
    }

    /**
     * Check whether this image is stored on the local public disk.
     */
    public function isLocalFile(): bool
    {
        return ! str_starts_with($this->image_path, 'http://')
            && ! str_starts_with($this->image_path, 'https://');
    }
}
