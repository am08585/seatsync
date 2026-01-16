<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Screening;
use App\Models\Theater;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Screening>
 */
class ScreeningFactory extends Factory
{
    protected $model = Screening::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('+1 day', '+30 days');
        $endTime = (clone $startTime)->modify('+2 hours');

        return [
            'movie_id' => Movie::factory(),
            'theater_id' => Theater::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'base_price' => fake()->randomElement([1200, 1500, 1800, 2000]), // in cents
        ];
    }
}
