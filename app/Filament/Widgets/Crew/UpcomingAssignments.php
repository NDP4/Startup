<?php

namespace App\Filament\Widgets\Crew;

use App\Models\CrewAssignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class UpcomingAssignments extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::user()->role === 'crew';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CrewAssignment::query()
                    ->where('crew_id', Auth::id())
                    ->whereIn('status', ['assigned', 'on_duty'])
                    ->whereHas('booking', function ($query) {
                        $query->where('booking_date', '>=', now());
                    })
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking.booking_date')
                    ->label('Tanggal Keberangkatan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('booking.bus.name')
                    ->label('Bus'),
                Tables\Columns\TextColumn::make('booking.pickup_location')
                    ->label('Lokasi Jemput'),
                Tables\Columns\TextColumn::make('booking.destination')
                    ->label('Tujuan'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'assigned' => 'warning',
                        'on_duty' => 'info',
                        'completed' => 'success',
                        default => 'gray',
                    }),
            ]);
    }
}
