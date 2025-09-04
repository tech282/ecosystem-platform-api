<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'bookings';

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';

    protected $fillable = [
        'provider_id',
        'customer_id',
        'service_name',
        'service_price',
        'service_duration',
        'booking_date',
        'start_time',
        'end_time',
        'status',
        'stripe_payment_intent_id',
        'total_amount',
        'platform_fee',
        'provider_payout',
        'customer_notes',
        'provider_notes',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'confirmation_code',
        'is_guest_booking',
        'completed_at',
    ];

    protected $casts = [
        'service_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'provider_payout' => 'decimal:2',
        'service_duration' => 'integer',
        'booking_date' => 'date',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_guest_booking' => 'boolean',
    ];

    /**
     * Get the provider for this booking
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    /**
     * Get the customer for this booking
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    /**
     * Get the user who cancelled this booking
     */
    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by', 'id');
    }

    /**
     * Get all transactions for this booking
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the review for this booking
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Scope for pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope for completed bookings
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for cancelled bookings
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope for upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString())
                    ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])
            && $this->booking_date > now()->addHours(24);
    }

    /**
     * Check if booking is active
     */
    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }
}
