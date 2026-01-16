<?php

namespace Database\Factories;

use App\Models\Theater;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Theater>
 */
class TheaterFactory extends Factory
{
    protected $model = Theater::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $theaterNames = ['Downtown Cinema', 'Plaza Theater', 'Uptown Multiplex', 'Grand Theatre', 'Riverside Cinema'];

        return [
            'name' => fake()->unique()->randomElement($theaterNames),
            'total_seats' => 96, // 8 rows x 12 seats
        ];
    }
}
