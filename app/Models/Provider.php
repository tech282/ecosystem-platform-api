<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Provider extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'providers';

    protected $fillable = [
        'user_id',
        'display_name',
        'slug',
        'tagline',
        'bio',
        'cover_image_url',
        'profile_image_url',
        'gallery_images',
        'location_city',
        'location_state',
        'location_country',
        'hourly_rate',
        'currency',
        'services',
        'years_experience',
        'completed_bookings',
        'average_rating',
        'total_reviews',
        'is_verified',
        'is_active',
        'stripe_connect_account_id',
        'stripe_onboarding_complete',
        'commission_rate',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'services' => 'array',
        'hourly_rate' => 'decimal:2',
        'average_rating' => 'decimal:1',
        'commission_rate' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'stripe_onboarding_complete' => 'boolean',
        'completed_bookings' => 'integer',
        'total_reviews' => 'integer',
        'years_experience' => 'integer',
    ];

    protected $hidden = [
        'stripe_connect_account_id',
    ];

    /**
     * Get the user that owns the provider profile
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get all bookings for this provider
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all reviews for this provider
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get all testimonials for this provider
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(ProviderTestimonial::class);
    }

    /**
     * Get provider availability
     */
    public function availability(): HasMany
    {
        return $this->hasMany(ProviderAvailability::class);
    }

    /**
     * Get provider blocked slots
     */
    public function blockedSlots(): HasMany
    {
        return $this->hasMany(ProviderBlockedSlot::class);
    }

    /**
     * Scope for active providers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for verified providers
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for providers with Stripe onboarding complete
     */
    public function scopeStripeReady($query)
    {
        return $query->where('stripe_onboarding_complete', true);
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
