<?php

namespace App\Events;

use App\Models\Endpoint;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResponseTimeChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Endpoint $endpoint,
        public float $newResponseTime
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('endpoints'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'EndpointResponseTimeChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'title' => 'Warning',
            'endpoint' => $this->endpoint->name,
            'response_time' => $this->newResponseTime,
            'message' => "Endpoint {$this->endpoint->name} response time is {$this->newResponseTime}",
            'background' => '#75dfed',
            'level' => 'info',
        ];
    }
}
