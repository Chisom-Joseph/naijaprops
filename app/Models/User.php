<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'phone',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Properties owned/listed by this user.
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    /**
     * Enquiries sent by this user.
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class, 'user_id');
    }

    /**
     * Properties saved/bookmarked by this user.
     */
    public function savedProperties(): HasMany
    {
        return $this->hasMany(SavedProperty::class, 'user_id');
    }

    /**
     * Reports filed by this user.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reported_by');
    }

    /**
     * Role helper methods.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    public function canListProperties(): bool
    {
        return in_array($this->role, ['admin', 'owner', 'agent'], true);
    }

    /**
     * Human-readable role badge label.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'owner' => 'Property Owner',
            'agent' => 'Real Estate Agent',
            'buyer' => 'Buyer / Tenant',
            default => ucfirst($this->role),
        };
    }

    /**
     * Determine the home dashboard route name for this user's role.
     */
    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            default => 'dashboard',
        };
    }
}
