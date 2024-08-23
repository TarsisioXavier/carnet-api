<?php

namespace Database\Factories;

use App\Models\Carnet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Installment>
 */
class InstallmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'carnet_id' => Carnet::factory(),
            'due_on' => fake()->dateTimeBetween('now', '+30 days'),
            'number' => fake()->numberBetween(1, 64),
            'value' => fake()->numberBetween(200, 5_000) / fake()->randomElement([10, 100]),
            'down_payment' => fake()->boolean(20),
        ];
    }
}
