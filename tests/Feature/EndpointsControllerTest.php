<?php

use App\Models\Endpoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class EndpointsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_paginated_endpoints()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $endpoint = Endpoint::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson(route('api.endpoints.index', [
            'per_page' => 5,
        ]));

        // Assert: Pagination and content
        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links',
            'last_page',
        ]);
        $this->assertCount(5, $response->json('data'));
        $this->assertEquals(5, $response->json('total'));
    }

    public function test_user_can_create_endpoint()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Session::start();

        $endpointData = [
            'name' => 'Test Added Endpoint',
            'url' => 'https://test.com',
            'method' => 'GET',
            'check_interval' => 5,
            'alert_threshold' => 95,
            'user_id' => $user->id,
        ];

        $response = $this->postJson(
            route('api.endpoints.store'),
            $endpointData,
            [
                'X-Csrf-Token' => csrf_token(),
            ]
        );

        $response->assertCreated();
        $this->assertDatabaseHas('endpoints', $endpointData);
    }

    public function test_user_can_view_endpoint()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $endpoint = Endpoint::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson(
            route('api.endpoints.show',
                [
                    'endpoint' => $endpoint->id,
                ])
        );

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $endpoint->id,
            'name' => $endpoint->name,
        ]);
    }

    public function test_user_can_update_endpoint()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Session::start();

        $endpoint = Endpoint::factory()->create(['user_id' => $user->id]);

        $updateData = [
            'name' => 'Updated Endpoint Name',
            'alert_threshold' => 75,
            'url' => 'https://test.com',
            'method' => 'GET',
            'check_interval' => 5,
            'user_id' => $user->id,
        ];

        $response = $this->putJson(
            route('api.endpoints.update',
                [
                    'endpoint' => $endpoint->id,
                ]),
            $updateData,
            [
                'X-Csrf-Token' => csrf_token(),
            ]
        );

        $response->assertOk();
        $this->assertDatabaseHas('endpoints', $updateData);
    }

    public function test_user_can_delete_post()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Session::start();

        $endpoint = Endpoint::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson(
            route('api.endpoints.destroy', [
                'endpoint' => $endpoint->id,
            ]),
            [],
            [
                'X-Csrf-Token' => csrf_token(),
            ]
        );

        $response->assertNoContent();
        $this->assertDatabaseMissing('endpoints', ['id' => $endpoint->id]);
    }
}
