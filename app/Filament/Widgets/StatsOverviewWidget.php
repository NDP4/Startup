<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\CrewAssignment;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => $this->getAdminStats(),
            'customer' => $this->getCustomerStats(),
            'crew' => $this->getCrewStats(),
            default => [],
        };
    }

    protected function getAdminStats(): array
    {
        $startDate = !is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            now()->startOfMonth();

        $endDate = !is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $totalRevenue = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $activeBuses = Bus::where('status', 'available')->count();

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue))
                ->description('Periode ' . $startDate->format('d M') . ' - ' . $endDate->format('d M'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart(Booking::where('payment_status', 'paid')
                    ->latest()
                    ->take(7)
                    ->pluck('total_amount')
                    ->toArray())
                ->color('success'),

            Stat::make('Total Booking', number_format($totalBookings))
                ->description('Semua transaksi booking')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart(Booking::latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('info'),

            Stat::make('Bus Aktif', number_format($activeBuses))
                ->description('Siap untuk disewa')
                ->descriptionIcon('heroicon-m-truck')
                ->chart(Bus::where('status', 'available')
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('success'),
        ];
    }

    protected function getCustomerStats(): array
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
                ->description('Total booking yang dibuat')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart(Booking::where('customer_id', $user->id)
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('info'),

            Stat::make('Perjalanan Selesai', number_format($completedBookings))
                ->description('Booking yang telah selesai')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart(Booking::where('customer_id', $user->id)
                    ->where('status', 'completed')
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('success'),

            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalSpent))
                ->description('Total biaya booking')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart(Booking::where('customer_id', $user->id)
                    ->where('payment_status', 'paid')
                    ->latest()
                    ->take(7)
                    ->pluck('total_amount')
                    ->toArray())
                ->color('warning'),
        ];
    }

    protected function getCrewStats(): array
    {
        $user = Auth::user();

        $totalAssignments = CrewAssignment::where('crew_id', $user->id)->count();
        $completedAssignments = CrewAssignment::where('crew_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $upcomingAssignments = CrewAssignment::where('crew_id', $user->id)
            ->whereIn('status', ['assigned', 'on_duty'])
            ->whereHas('booking', function ($query) {
                $query->where('booking_date', '>=', now());
            })
            ->count();

        return [
            Stat::make('Total Penugasan', number_format($totalAssignments))
                ->description('Total tugas diterima')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart(CrewAssignment::where('crew_id', $user->id)
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('info'),

            Stat::make('Tugas Selesai', number_format($completedAssignments))
                ->description('Tugas yang diselesaikan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart(CrewAssignment::where('crew_id', $user->id)
                    ->where('status', 'completed')
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('success'),

            Stat::make('Tugas Mendatang', number_format($upcomingAssignments))
                ->description('Tugas yang akan datang')
                ->descriptionIcon('heroicon-m-calendar')
                ->chart(CrewAssignment::where('crew_id', $user->id)
                    ->whereIn('status', ['assigned', 'on_duty'])
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('warning'),
        ];
    }
}
