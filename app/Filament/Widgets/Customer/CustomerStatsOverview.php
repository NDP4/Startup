<?php

namespace App\Filament\Widgets\Customer;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CustomerStatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return Auth::user()->role === 'customer';
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        $totalBookings = Booking::where('customer_id', $user->id)->count();
        $completedBookings = Booking::where('customer_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $totalSpent = Booking::where('customer_id', $user->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        return [
            Stat::make('Total Booking', number_format($totalBookings))
                ->description('Total booking yang pernah dibuat')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart(Booking::where('customer_id', $user->id)
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('info'),

            Stat::make('Booking Selesai', number_format($completedBookings))
                ->description('Jumlah booking yang telah selesai')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart(Booking::where('customer_id', $user->id)
                    ->where('status', 'completed')
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('success'),

            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalSpent, 0, ',', '.'))
                ->description('Total biaya booking yang telah dibayar')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
