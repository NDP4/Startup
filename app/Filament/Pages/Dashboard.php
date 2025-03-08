<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('bookingStatus')
                            ->label('Status Booking')
                            ->options([
                                'all' => 'Semua Status',
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('all'),
                        Select::make('paymentStatus')
                            ->label('Status Pembayaran')
                            ->options([
                                'all' => 'Semua Status',
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                            ])
                            ->default('all'),
                        DatePicker::make('startDate')
                            ->label('Tanggal Mulai')
                            ->maxDate(fn(Get $get) => $get('endDate') ?: now()),
                        DatePicker::make('endDate')
                            ->label('Tanggal Akhir')
                            ->minDate(fn(Get $get) => $get('startDate') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(4),
            ]);
    }
}
