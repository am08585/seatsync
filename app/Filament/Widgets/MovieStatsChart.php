<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MovieStatsChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Movie Popularity (Reservations per Movie)';

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        // Get movies with reservation counts through screenings using a database query
        $movieStats = DB::table('movies')
            ->select([
                'movies.title',
                DB::raw('COUNT(reservations.id) as reservation_count'),
            ])
            ->join('screenings', 'movies.id', '=', 'screenings.movie_id')
            ->join('reservations', 'screenings.id', '=', 'reservations.screening_id')
            ->where('reservations.status', 'confirmed')
            ->whereNull('movies.deleted_at')
            ->whereNull('screenings.deleted_at')
            ->groupBy('movies.id', 'movies.title')
            ->orderBy('reservation_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Confirmed Reservations',
                    'data' => $movieStats->pluck('reservation_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(251, 146, 60, 0.8)',
                        'rgba(107, 114, 128, 0.8)',
                        'rgba(217, 70, 239, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(251, 191, 36, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(168, 85, 247, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(20, 184, 166, 1)',
                        'rgba(251, 146, 60, 1)',
                        'rgba(107, 114, 128, 1)',
                        'rgba(217, 70, 239, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $movieStats->pluck('title')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
