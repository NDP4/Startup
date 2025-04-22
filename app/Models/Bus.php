<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number_plate',
        'description',
        'default_seat_capacity',
        'status', // enum: available, maintenance, booked
        'images',
        'image_names',
        'pricing_type', // daily atau distance
        'price_per_day', // harga per hari
        'price_per_km', // harga per kilometer
        'legrest_price_per_seat',
    ];

    protected $casts = [
        'images' => 'array',
        'image_names' => 'array',
        'price_per_day' => 'decimal:2',
        'price_per_km' => 'decimal:2',
        'legrest_price_per_seat' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bus) {
            if (is_array($bus->images)) {
                $bus->images = array_values($bus->images);
            }
        });
    }

    public function seatConfigurations(): HasMany
    {
        return $this->hasMany(SeatConfiguration::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function calculateTotalPrice(int $totalSeats, string $seatType, int $days = 1, float $distance = 0): float
    {
        $basePrice = match ($this->pricing_type) {
            'daily' => $this->price_per_day * $days,
            'distance' => $this->price_per_km * $distance,
            default => 0,
        };

        $seatPrice = match ($seatType) {
            'legrest' => $this->legrest_price_per_seat * $totalSeats,
            default => 0,
        };

        return $basePrice + $seatPrice;
    }

    public function isAvailableOn($startDate, $endDate = null): bool
    {
        // Jika bus dalam maintenance/booked, langsung return false
        if ($this->status !== 'available') {
            return false;
        }

        // Jika tidak ada tanggal akhir, gunakan tanggal awal
        $endDate = $endDate ?? $startDate;

        // Cek apakah ada booking yang overlap
        $conflictingBookings = $this->bookings()
            ->where('payment_status', 'paid')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('booking_date', [$startDate, $endDate])
                        ->orWhereBetween('return_date', [$startDate, $endDate])
                        ->orWhere(function ($sq) use ($startDate, $endDate) {
                            $sq->where('booking_date', '<=', $startDate)
                                ->where('return_date', '>=', $endDate);
                        });
                });
            })
            ->exists();

        return !$conflictingBookings;
    }

    public function getMainImageAttribute()
    {
        if (!$this->images || empty($this->images)) {
            return null;
        }
        return is_array($this->images[0]) ? $this->images[0]['url'] : $this->images[0];
    }

    public function getAllImagesAttribute()
    {
        if (!$this->images) {
            return [];
        }
        return collect($this->images)->map(function ($image) {
            return is_array($image) ? $image : ['url' => $image, 'description' => null];
        })->toArray();
    }
}
