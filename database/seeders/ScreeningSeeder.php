<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Screening;
use App\Models\Theater;
use Illuminate\Database\Seeder;

class ScreeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = Movie::all();
        $theaters = Theater::all();

        $baseDate = now()->addDays(2);
        $times = ['14:00', '17:00', '20:00'];
        $basePrices = [1200, 1500, 1800]; // in cents

        foreach ($movies as $movieIndex => $movie) {
            foreach ($theaters as $theaterIndex => $theater) {
                foreach ($times as $timeIndex => $time) {
                    $startTime = $baseDate
                        ->clone()
                        ->addDays($movieIndex + $theaterIndex)
                        ->setTimeFromTimeString($time);

                    $endTime = $startTime->clone()->addHours(2);

                    Screening::firstOrCreate(
                        [
                            'movie_id' => $movie->id,
                            'theater_id' => $theater->id,
                            'start_time' => $startTime,
                        ],
                        [
                            'end_time' => $endTime,
                            'base_price' => $basePrices[$timeIndex] ?? 1200,
                        ]
                    );
                }
            }
        }
    }
}
