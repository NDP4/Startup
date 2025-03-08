<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Traits\HasMidtransPayment;
use Illuminate\Support\Facades\Auth; // Add this import

class Booking extends Model
{
    use HasFactory, HasMidtransPayment;

    protected $fillable = [
        'customer_id',
        'bus_id',
        'booking_date',
        'return_date',
        'total_seats',
        'seat_type',
        'pickup_location',
        'destination',
        'status', // enum: pending, confirmed, completed, cancelled
        'total_amount',
        'payment_status', // enum: pending, paid, failed
        'payment_token',
        'snap_token', // Add this line
        'special_requests',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'return_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function crewAssignments(): HasMany
    {
        return $this->hasMany(CrewAssignment::class);
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function canRetryPayment(): bool
    {
        return in_array($this->payment_status, ['pending', 'failed'])
            && !empty($this->snap_token);
    }

    public function busReview(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function crewReview(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function canBeReviewed(): bool
    {
        return $this->status === 'completed' &&
            $this->payment_status === 'paid' &&
            !$this->review;
    }

    public function canEditPriceFactors(): bool
    {
        if (Auth::user()?->role === 'admin') {
            return true;
        }
        return $this->payment_status !== 'paid';
    }
}
