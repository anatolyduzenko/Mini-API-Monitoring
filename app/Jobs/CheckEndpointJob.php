<?php

namespace App\Jobs;

use App\Models\Endpoint;
use App\Models\EndpointLog;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckEndpointJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $endpoint;

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
    public function handle(): void
    {
        $cacheKey = "endpoint_check::{$this->endpoint->id}";
        $lastChecked = Cache::get($cacheKey);
        $interval = $this->endpoint->check_interval * 60;

        if ($lastChecked && (now()->timestamp - $lastChecked) < $interval) {
            Log::info("Skipping {$this->endpoint->name}, checked recently.");

            return;
        }

        try {
            $start = microtime(true);
            $response = Http::timeout(10)->send($this->endpoint->method, $this->endpoint->url);
            $time = round((microtime(true) - $start) * 1000);

            Log::info("Checked {$this->endpoint->name}: {$response->status()} in {$time}ms");

            EndpointLog::create([
                'endpoint_id' => $this->endpoint->id,
                'status_code' => $response->status(),
                'response_time' => $time,
                'created_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to check {$this->endpoint->name}: ".$e->getMessage());

            EndpointLog::create([
                'endpoint_id' => $this->endpoint->id,
                'status_code' => 0,
                'response_time' => null,
                'created_at' => now(),
            ]);
        }

        Cache::put($cacheKey, now()->timestamp);

        EvaluateUptimeJob::dispatch($this->endpoint);
    }
}
