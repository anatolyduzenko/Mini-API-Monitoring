<?php

namespace Tests\Unit;

use App\Enums\StatusCode;
use App\Models\Endpoint;
use App\Models\User;
use App\Services\CheckEndpointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CheckEndpointServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_checks_endpoint_and_logs_success()
    {
        Http::fake([
            'https://example.com/*' => Http::response('OK', StatusCode::OK->value),
        ]);

        Log::shouldReceive('info')->once();
        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->once();

        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'url' => 'https://example.com/status',
            'method' => 'GET',
            'check_interval' => 1,
            'auth_type' => null,
            'user_id' => $user->id,
        ]);

        $service = new CheckEndpointService;

        $this->assertTrue($service->shouldCheck($endpoint));

        $service->check($endpoint);

        $this->assertDatabaseHas('endpoint_logs', [
            'endpoint_id' => $endpoint->id,
            'status_code' => StatusCode::OK->value,
        ]);
    }

    public function test_it_checks_endpoint_with_basic_auth()
    {
        Http::fake([
            'https://basic.test/*' => Http::response('OK', StatusCode::OK->value),
        ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->once();
        Log::shouldReceive('info')->once();

        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'url' => 'https://basic.test/status',
            'method' => 'GET',
            'check_interval' => 1,
            'auth_type' => 'basic',
            'username' => 'john',
            'password' => 'secret',
            'user_id' => $user->id,
        ]);

        $service = new CheckEndpointService;
        $service->check($endpoint);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Basic '.base64_encode('john:secret'));
        });

        $this->assertDatabaseHas('endpoint_logs', [
            'endpoint_id' => $endpoint->id,
            'status_code' => StatusCode::OK->value,
        ]);
    }

    public function test_it_fetches_token_and_checks_with_token_auth()
    {
        $fakeToken = 'abc123';

        Http::fake([
            'https://auth.test/token' => Http::response(['token' => $fakeToken], StatusCode::OK->value),
            'https://token.test/status' => Http::response('OK', StatusCode::OK->value),
        ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->once();
        Log::shouldReceive('info')->once();

        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'url' => 'https://token.test/status',
            'auth_url' => 'https://auth.test/token',
            'method' => 'GET',
            'check_interval' => 1,
            'auth_type' => 'token',
            'username' => 'api-user',
            'password' => 'api-pass',
            'auth_token' => null,
            'auth_token_name' => 'token',
            'user_id' => $user->id,
        ]);

        $service = new CheckEndpointService;
        $service->check($endpoint);

        Http::assertSent(function ($request) use ($fakeToken) {
            return $request->url() === 'https://token.test/status'
                && $request->hasHeader('Authorization', 'Bearer '.$fakeToken);
        });

        $this->assertDatabaseHas('endpoint_logs', [
            'endpoint_id' => $endpoint->id,
            'status_code' => StatusCode::OK->value,
        ]);

        $this->assertEquals($fakeToken, $endpoint->fresh()->auth_token);
    }
}
