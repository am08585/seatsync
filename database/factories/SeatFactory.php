<?php

namespace Database\Factories;

use App\Models\Seat;
use App\Models\Theater;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seat>
 */
class SeatFactory extends Factory
{
    protected $model = Seat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $seatTypes = ['standard', 'vip', 'premium', 'wheelchair'];
        $selectedType = fake()->randomElement($seatTypes);

        $priceModifiers = [
            'standard' => 0,
            'vip' => 400,
            'premium' => 200,
            'wheelchair' => 0,
        ];

        return [
            'theater_id' => Theater::factory(),
            'row' => fake()->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']),
            'number' => fake()->numberBetween(1, 12),
            'seat_type' => $selectedType,
            'price_modifier' => $priceModifiers[$selectedType],
        ];
    }
}
