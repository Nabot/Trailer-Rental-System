<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trailer>
 */
class TrailerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'type' => 'Single Axle',
            'axle' => 'Single',
            'size_m' => 2.5,
            'rate_per_day' => 500,
            'required_deposit' => 1000,
            'status' => 'available',
            'description' => null,
            'registration_number' => null,
            'notes' => null,
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'available']);
    }
}
