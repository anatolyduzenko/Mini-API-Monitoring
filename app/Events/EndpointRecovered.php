<?php

namespace App\Events;

use App\Models\Endpoint;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EndpointRecovered implements ShouldBroadcastNow
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
        return 'EndpointRecovered';
    }

    public function broadcastWith(): array
    {
        return [
            'title' => 'Recovered',
            'endpoint' => $this->endpoint->name,
            'status' => $this->status,
            'message' => "Endpoint {$this->endpoint->name} is back with status {$this->status}",
            'background' => '#8dffd2',
            'level' => 'success',
        ];
    }
}
