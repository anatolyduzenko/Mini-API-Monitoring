<?php

namespace Database\Factories;

use App\Models\Endpoint;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Endpoint>
 */
class EndpointLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'endpoint_id' => Endpoint::factory(),
            'status_code' => fake()->randomElement([200, 201, 301, 302, 403, 500]),
            'response_time' => fake()->randomNumber(4),
            'created_at' => Carbon::now()->subDays(rand(0, 60)),
            'updated_at' => Carbon::now(),
        ];
    }
}
