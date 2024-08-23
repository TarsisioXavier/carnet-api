<?php

namespace Database\Factories;

use App\Models\Types\CarnetPeriodicity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Carnet>
 */
class CarnetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'value' => fake()->numberBetween(2_000, 500_000) / fake()->randomElement([10, 100]),
            'installments_count' => 6 * fake()->numberBetween(1, 10),
            'first_due_date' => Carbon::make(fake()->dateTimeBetween('now', '+30 days')),
            'periodicity' => fake()->randomElement(CarnetPeriodicity::cases()),
            'down_payment' => 0,
        ];
    }

    /**
     * Sets a value for a "down payment".
     *
     * @return CarnetFactory
     */
    public function withDownPayment(): CarnetFactory
    {
        return $this->state(function ($attributes) {
            return [
                'down_payment' => $attributes['value'] * (fake()->numberBetween(5, 20) / 100),
            ];
        });
    }

    /**
     * Sets the carnet with weekly monthly.
     *
     * @return CarnetFactory
     */
    public function monthly()
    {
        return $this->state(function ($attributes) {
            $createdAt = Carbon::make(fake()->dateTimeBetween('-2 years'));

            return [
                'periodicity' => CarnetPeriodicity::Monthly,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'first_due_date' => $createdAt->clone()->addMonth()->startOfMonth(),
            ];
        });
    }

    /**
     * Sets the carnet with weekly payments.
     *
     * @return CarnetFactory
     */
    public function weekly(): CarnetFactory
    {
        return $this->state(function ($attributes) {
            $createdAt = Carbon::make(fake()->dateTimeBetween('-2 years'));

            return [
                'periodicity' => CarnetPeriodicity::Weekly,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
                'first_due_date' => $createdAt->clone()->addWeek()->startOfWeek(),
            ];
        });
    }
}
