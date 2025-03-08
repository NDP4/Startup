<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'bus_id',
        'crew_id',
        'bus_rating',
        'bus_comment',
        'crew_rating',
        'crew_comment',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('access', function (Builder $query) {
            if (!Auth::check()) return;

            $user = Auth::user();

            switch ($user->role) {
                case 'customer':
                    $query->whereHas('booking', function ($q) use ($user) {
                        $q->where('customer_id', $user->id);
                    });
                    break;
                case 'crew':
                    $query->where('crew_id', $user->id);
                    break;
            }
        });
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function crew(): BelongsTo
    {
        return $this->belongsTo(User::class, 'crew_id');
    }
}
