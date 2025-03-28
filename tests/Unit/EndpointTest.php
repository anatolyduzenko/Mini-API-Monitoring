<?php

namespace Tests\Unit;

use App\Models\Endpoint;
use App\Models\EndpointLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_user_can_create_an_endpoint()
    {
        $user = User::factory()->create();

        $endpoint = Endpoint::create([
            'name' => 'Test API',
            'url' => 'https://example.com/api',
            'method' => 'GET',
            'headers' => json_encode(['Authorization' => 'Bearer token']),
            'body' => json_encode(['param' => 'value']),
            'check_interval' => 60,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('endpoints', ['name' => 'Test API']);
        $this->assertInstanceOf(Endpoint::class, $endpoint);
    }

    public function test_that_endpoint_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $endpoint->user);
        $this->assertEquals($user->id, $endpoint->user->id);
    }

    public function test_that_endpoint_has_many_logs()
    {
        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create(['user_id' => $user->id]);
        $log1 = EndpointLog::factory()->create(['endpoint_id' => $endpoint->id]);
        $log2 = EndpointLog::factory()->create(['endpoint_id' => $endpoint->id]);

        $this->assertCount(2, $endpoint->logs);
        $this->assertTrue($endpoint->logs->contains($log1));
        $this->assertTrue($endpoint->logs->contains($log2));
    }
}
