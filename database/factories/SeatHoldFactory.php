<?php

namespace Database\Factories;

use App\Models\Screening;
use App\Models\Seat;
use App\Models\SeatHold;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SeatHold>
 */
class SeatHoldFactory extends Factory
{
    protected $model = SeatHold::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = fake()->dateTimeBetween('-1 hour', 'now');

        return [
            'user_id' => User::factory(),
            'screening_id' => Screening::factory(),
            'seat_id' => Seat::factory(),
            'hold_token' => Str::random(32),
            'expires_at' => (clone $createdAt)->modify('+10 minutes'),
            'created_at' => $createdAt,
        ];
    }
}
