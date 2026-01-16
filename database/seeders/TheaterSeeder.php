<?php

namespace Database\Seeders;

use App\Models\Theater;
use Illuminate\Database\Seeder;

class TheaterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $theaters = [
            ['name' => 'Downtown Cinema', 'total_seats' => 96],
            ['name' => 'Plaza Theater', 'total_seats' => 96],
        ];

        foreach ($theaters as $theaterData) {
            Theater::firstOrCreate(['name' => $theaterData['name']], $theaterData);
        }
    }
}
