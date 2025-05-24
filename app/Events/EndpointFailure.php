<?php

namespace App\Events;

use App\Models\Endpoint;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EndpointFailure implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(protected Endpoint $endpoint, protected int $status) {}

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
        return 'EndpointFailure';
    }

    public function broadcastWith(): array
    {
        return [
            'title' => 'Failed',
            'endpoint' => $this->endpoint->name,
            'status' => $this->status,
            'message' => "Endpoint {$this->endpoint->name} failed with status {$this->status}",
            'background' => '#ffc8cf',
            'level' => 'error',
        ];
    }
}
