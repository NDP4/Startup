<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return Auth::user()->role === 'admin';
    }

    protected function getStats(): array
    {
        $totalRevenue = Booking::where('payment_status', 'paid')
            ->sum('total_amount');

        $totalCustomers = User::where('role', 'customer')->count();

        $totalBookings = Booking::count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $completionRate = $totalBookings > 0 ? ($completedBookings / $totalBookings) * 100 : 0;

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total dari semua booking yang sudah dibayar')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart(Booking::where('payment_status', 'paid')
                    ->latest()
                    ->take(7)
                    ->pluck('total_amount')
                    ->toArray())
                ->color('success'),

            Stat::make('Total Pelanggan', number_format($totalCustomers))
                ->description('Jumlah pelanggan terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->chart(User::where('role', 'customer')
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('info'),

            Stat::make('Tingkat Penyelesaian', number_format($completionRate, 1) . '%')
                ->description('Persentase booking yang selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([$completionRate, 100 - $completionRate])
                ->color('success'),
        ];
    }
}
