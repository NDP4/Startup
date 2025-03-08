<?php

namespace App\Filament\Widgets\Customer;

use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RecentBookings extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::user()->role === 'customer';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->where('customer_id', Auth::id())
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_date')
                    ->label('Tanggal Booking')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bus.name')
                    ->label('Bus'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('idr'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'primary',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
            ]);
    }
}
