<?php

namespace App\Jobs;

use App\Models\Endpoint;
use App\Services\EndpointCheckerService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckEndpointJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $endpoint;

    protected float $start;

    /**
     * Create a new job instance.
     */
    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Execute the job.
     */
    public function handle(EndpointCheckerService $service): void
    {
        if (! $service->shouldCheck($this->endpoint)) {
            Log::info("Skipping {$this->endpoint->name}, checked recently.");

            return;
        }

        $service->check($this->endpoint);

        EvaluateUptimeJob::dispatch($this->endpoint);
    }
}
