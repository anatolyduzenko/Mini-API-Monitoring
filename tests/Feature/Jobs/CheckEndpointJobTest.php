<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use App\Models\Endpoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\CheckEndpointJob;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CheckEndpointJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_logs_successful_response()
    {
        Cache::flush();

        Http::fake([
            'https://example.com' => Http::response([], 200)
        ]);

        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'method' => 'GET',
        ]);

        CheckEndpointJob::dispatchSync($endpoint);

        $this->assertDatabaseHas('endpoint_logs', [
            'endpoint_id' => $endpoint->id,
            'status_code' => 200,
        ]);
    }

    public function test_it_logs_failure_when_request_fails()
    {
        Cache::flush();
        
        Http::fake([
            'https://broken.com' => Http::sequence()->pushStatus(500),
        ]);

        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://broken.com',
        ]);

        CheckEndpointJob::dispatchSync($endpoint);

        $this->assertDatabaseHas('endpoint_logs', [
            'endpoint_id' => $endpoint->id,
            'status_code' => 500,
        ]);
    }
}
