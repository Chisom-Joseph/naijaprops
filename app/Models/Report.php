<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    public const STATUSES = [
        'pending' => 'Pending Review',
        'reviewed' => 'Under Review',
        'resolved' => 'Resolved',
    ];

    /**
     * Common Nigerian real estate report reason templates for quick user selection.
     */
    public const REASON_TEMPLATES = [
        'Upfront inspection or registration fee requested before physical viewing',
        'Suspected scam or impersonation of property owner/agent',
        'Misleading photos, inaccurate price, or wrong Nigerian location',
        'Property is already sold or rented out',
        'Duplicate or spam property listing',
    ];

    /**
     * The table only uses created_at per the ERD.
     */
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'property_id',
        'reported_by',
        'reason',
        'status',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * The property that was reported.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * The user who submitted the report.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Human-readable report status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
