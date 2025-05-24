<?php

namespace Tests\Feature;

use App\Events\EndpointFailure;
use App\Events\EndpointRecovered;
use App\Listeners\Monitoring\DTO\StatusChangeDTO;
use App\Listeners\Monitoring\StatusChangeHandler;
use App\Models\Endpoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class StatusChangeHandlerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_triggers_failure_event_after_threshold()
    {
        Event::fake([EndpointFailure::class]);
        Cache::shouldReceive('get')
            ->once()
            ->andReturn([
                'last_status' => 200,
                'failure_count' => 4,
                'notified' => false,
            ]);
        Cache::shouldReceive('put')->once();

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

        $handler = new StatusChangeHandler;
        $dto = new StatusChangeDTO(
            endpoint: $endpoint,
            currentStatus: 500
        );

        $handler->handle($dto);

        Event::assertDispatched(EndpointFailure::class);
    }

    public function test_it_triggers_recovery_event_after_success()
    {
        Event::fake();
        Cache::shouldReceive('get')
            ->once()
            ->andReturn([
                'last_status' => 500,
                'failure_count' => 5,
                'notified' => true,
            ]);
        Cache::shouldReceive('put')->once();

        $handler = new StatusChangeHandler;
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
        $dto = new StatusChangeDTO(
            endpoint: $endpoint,
            currentStatus: 200
        );

        $handler->handle($dto);

        Event::assertDispatched(EndpointRecovered::class);
    }

    // public function test_it_does_not_dispatch_any_event_below_threshold()
    // {
    //     Event::fake();
    //     Cache::shouldReceive('get')
    //         ->once()
    //         ->andReturn([
    //             'last_status' => 200,
    //             'failure_count' => 2,
    //             'notified' => false,
    //         ]);
    //     Cache::shouldReceive('put')->once();

    //     $handler = new StatusChangeHandler();
    //     $user = User::factory()->create();
    //     $endpoint = Endpoint::create([
    //         'name' => 'Test API',
    //         'url' => 'https://example.com/api',
    //         'method' => 'GET',
    //         'headers' => json_encode(['Authorization' => 'Bearer token']),
    //         'body' => json_encode(['param' => 'value']),
    //         'check_interval' => 60,
    //         'user_id' => $user->id,
    //     ]);
    //     $dto = new StatusChangeDTO(
    //         endpoint: $endpoint,
    //         currentStatus: 404
    //     );

    //     $handler->handle($dto);

    //     Event::assertNothingDispatched();
    // }
}
