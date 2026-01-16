<?php

namespace Database\Seeders;

use App\Models\Seat;
use App\Models\Theater;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $theaters = Theater::all();
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $seatsPerRow = 12;

        foreach ($theaters as $theater) {
            foreach ($rows as $rowIndex => $row) {
                for ($seatNumber = 1; $seatNumber <= $seatsPerRow; $seatNumber++) {
                    // Determine seat type based on row
                    if ($rowIndex < 2) {
                        $seatType = 'standard';
                        $priceModifier = 0;
                    } elseif ($rowIndex < 5) {
                        $seatType = 'premium';
                        $priceModifier = 200;
                    } else {
                        $seatType = 'vip';
                        $priceModifier = 400;
                    }

                    // Add a few wheelchair accessible seats
                    if ($rowIndex == 0 && $seatNumber % 6 === 0) {
                        $seatType = 'wheelchair';
                        $priceModifier = 0;
                    }

                    Seat::firstOrCreate(
                        [
                            'theater_id' => $theater->id,
                            'row' => $row,
                            'number' => $seatNumber,
                        ],
                        [
                            'seat_type' => $seatType,
                            'price_modifier' => $priceModifier,
                        ]
                    );
                }
            }
        }
    }
}
