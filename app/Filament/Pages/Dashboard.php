<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int
    {
        return 4;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // \App\Filament\Widgets\StatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // \App\Filament\Widgets\RecentReservations::class,
            // \App\Filament\Widgets\MovieStatsChart::class,
        ];
    }
}
