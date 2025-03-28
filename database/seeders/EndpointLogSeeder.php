<?php

namespace Database\Seeders;

use App\Models\Endpoint;
use App\Models\EndpointLog;
use Illuminate\Database\Seeder;

class EndpointLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $endpoints = Endpoint::all();

        if ($endpoints->isEmpty()) {
            $endpoints = Endpoint::factory()->count(4)->create();
        }

        // Create logs for endpoints
        foreach ($endpoints as $endpoint) {
            EndpointLog::factory()->count(100)->create([
                'endpoint_id' => $endpoint->id,
            ]);
        }
    }
}
