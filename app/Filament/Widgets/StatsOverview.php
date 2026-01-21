<?php

namespace App\Filament\Widgets;

use App\Models\Movie;
use App\Models\Reservation;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Movies', Movie::count())
                ->description('All movies in the system')
                ->descriptionIcon('heroicon-m-film')
                ->color('primary'),

            Stat::make('Active Reservations', Reservation::where('status', 'confirmed')->count())
                ->description('Currently confirmed bookings')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),

            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Today\'s Revenue', '$'.number_format(Reservation::whereDate('created_at', today())->where('status', 'confirmed')->sum('total_price') / 100, 2))
                ->description('Revenue from confirmed reservations today')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),
        ];
    }
}
