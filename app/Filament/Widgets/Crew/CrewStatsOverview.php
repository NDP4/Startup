<?php

namespace App\Filament\Widgets\Crew;

use App\Models\CrewAssignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class CrewStatsOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return Auth::user()->role === 'crew';
    }

    protected function getStats(): array
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
                ->description('Total tugas yang pernah diterima')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart(CrewAssignment::where('crew_id', $user->id)
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('info'),

            Stat::make('Tugas Selesai', number_format($completedAssignments))
                ->description('Jumlah tugas yang telah diselesaikan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart(CrewAssignment::where('crew_id', $user->id)
                    ->where('status', 'completed')
                    ->latest()
                    ->take(7)
                    ->pluck('id')
                    ->toArray())
                ->color('success'),

            Stat::make('Tugas Mendatang', number_format($upcomingAssignments))
                ->description('Jumlah tugas yang akan datang')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),
        ];
    }
}
