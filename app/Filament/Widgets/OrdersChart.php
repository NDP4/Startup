<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Booking per Bulan';

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::user()->role === 'admin';
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = collect(range(1, 12))->map(function ($month) {
            return Booking::whereMonth('created_at', $month)
                ->whereYear('created_at', date('Y'))
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Booking',
                    'data' => $data->toArray(),
                    'fill' => 'start',
                    'borderColor' => '#1D4ED8',
                    'backgroundColor' => 'rgba(29, 78, 216, 0.1)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
