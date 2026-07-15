<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalBookings = Booking::count();
        $totalRevenue = Booking::where('status', 'active')->sum('total_price');
        $totalUsers = User::count();

        return [
            Stat::make('Total Booking', $totalBookings)
                ->description('Total pemesanan di sistem')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Dari booking yang aktif/selesai')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
            Stat::make('Total Pengguna', $totalUsers)
                ->description('Jumlah pengguna terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),
        ];
    }
}
