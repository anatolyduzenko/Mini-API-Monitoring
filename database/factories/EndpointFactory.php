<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Endpoint>
 */
class EndpointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'url' => fake()->unique()->url(),
            'method' => fake()->randomElement(['GET', 'HEAD', 'FETCH']),
            'headers' => '',
            'body' => '',
            'check_interval' => 5,
            'created_at' => now(),
        ];
    }
}
