<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the provider profile for this user
     */
    public function provider(): HasOne
    {
        return $this->hasOne(Provider::class, 'user_id', 'id');
    }

    /**
     * Get all bookings where this user is the customer
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id', 'id');
    }

    /**
     * Get all reviews written by this user
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'customer_id', 'id');
    }

    /**
     * Check if user is a provider
     */
    public function isProvider(): bool
    {
        return $this->provider()->exists();
    }

    /**
     * Check if user has an active provider profile
     */
    public function isActiveProvider(): bool
    {
        return $this->provider()->where('is_active', true)->exists();
    }

    /**
     * Check if user is a verified provider
     */
    public function isVerifiedProvider(): bool
    {
        return $this->provider()->where('is_verified', true)->exists();
    }

    /**
     * Get user's role (customer or provider)
     */
    public function getRole(): string
    {
        return $this->isProvider() ? 'provider' : 'customer';
    }
}
