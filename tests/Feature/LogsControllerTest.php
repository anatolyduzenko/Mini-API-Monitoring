<?php

use App\Enums\StatusCode;
use App\Models\Endpoint;
use App\Models\EndpointLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_logs_with_filters()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $endpoint = Endpoint::factory()->create([
            'user_id' => $user->id,
        ]);

        EndpointLog::factory()->count(3)->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => StatusCode::OK->value,
        ]);

        EndpointLog::factory()->count(2)->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => StatusCode::NOT_FOUND->value,
        ]);

        $response = $this->getJson(route('api.logs.index', [
            'per_page' => 5,
            'endpoint_id' => $endpoint->id,
            'status_code' => [StatusCode::OK->value],
        ]));

        // Assert: Pagination and content
        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links',
            'last_page',
        ]);
        $this->assertCount(3, $response->json('data'));
        $this->assertEquals(3, $response->json('total'));
    }
}
