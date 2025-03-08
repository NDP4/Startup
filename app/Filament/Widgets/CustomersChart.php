<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class CustomersChart extends ChartWidget
{
    public static function canView(): bool
    {
        return Auth::user()->role === 'admin';
    }

    protected static ?string $heading = 'Pertumbuhan Pelanggan';

    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = collect(range(1, 12))->map(function ($month) {
            return User::where('role', 'customer')
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', date('Y'))
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Pelanggan Baru',
                    'data' => $data->toArray(),
                    'fill' => 'start',
                    'borderColor' => '#059669',
                    'backgroundColor' => 'rgba(5, 150, 105, 0.1)',
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }
}
