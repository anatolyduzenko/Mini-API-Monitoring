<?php

namespace Tests\Feature;

use App\Events\ResponseTimeChanged;
use App\Listeners\Monitoring\DTO\ResponseTimeChangeDTO;
use App\Listeners\Monitoring\ResponseTimeChangeHandler;
use App\Models\Endpoint;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ResponseTimeChangeHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['broadcasting.default' => 'null']);
    }

    public function test_supports_returns_true_for_valid_dto()
    {
        $handler = new ResponseTimeChangeHandler;

        $dto = new ResponseTimeChangeDTO(Endpoint::factory()->make(), 100);
        $this->assertTrue($handler->supports($dto));
    }

    public function test_it_broadcasts_event_when_response_time_difference_exceeds_threshold()
    {
        Event::fake();
        Cache::put('endpoint:response_time:1', [
            'response_time' => 100.0,
            'notified' => false,
        ]);

        $user = User::factory()->make();
        $endpoint = Endpoint::factory()->make(['id' => 1, 'user_id' => $user->id]);
        $dto = new ResponseTimeChangeDTO($endpoint, 110.0);

        $handler = new ResponseTimeChangeHandler;
        $handler->handle($dto);

        Event::assertDispatched(ResponseTimeChanged::class, function ($event) use ($endpoint) {
            return $event->endpoint->id === $endpoint->id && $event->newResponseTime === 110.0;
        });

        $this->assertEquals([
            'response_time' => 110.0,
            'notified' => false,
        ], Cache::get('endpoint:response_time:1'));
    }

    public function test_it_does_not_broadcast_if_difference_is_below_threshold()
    {
        Event::fake();
        Cache::put('endpoint:response_time:1', [
            'response_time' => 100,
            'notified' => false,
        ]);

        $user = User::factory()->make();
        $endpoint = Endpoint::factory()->make(['id' => 1, 'user_id' => $user->id]);
        $dto = new ResponseTimeChangeDTO($endpoint, 102);

        $handler = new ResponseTimeChangeHandler;
        $handler->handle($dto);

        Event::assertNotDispatched(ResponseTimeChanged::class);

        $this->assertEquals([
            'response_time' => 102,
            'notified' => false,
        ], Cache::get('endpoint:response_time:1'));
    }
}
