<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestOrders extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => Booking::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Booking')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bus.name')
                    ->label('Bus')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('idr'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn(Booking $record): string => BookingResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
