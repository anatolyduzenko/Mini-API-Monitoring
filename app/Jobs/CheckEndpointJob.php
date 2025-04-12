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

        $httpClient = Http::timeout(10);

        if ($this->endpoint->auth_type === 'basic') {
            $httpClient = $httpClient->withBasicAuth($this->endpoint->username, $this->endpoint->password);
        }

        if ($this->endpoint->auth_type === 'token') {

            if ($this->endpoint->auth_token === null) {
                $authResponse = Http::asForm()->post($this->endpoint->auth_url, [
                    'username' => $this->endpoint->username,
                    'password' => $this->endpoint->password,
                ]);

                if ($authResponse->successful()) {
                    $tokenKey = $this->endpoint->auth_token_name ?? 'token';
                    $token = data_get($authResponse->json(), $tokenKey);

                    if ($token) {
                        $this->endpoint->auth_token = $token;
                        $this->endpoint->save();
                        $httpClient = $httpClient->withToken($token);
                    } else {
                        Log::warning("Token not found using key [{$tokenKey}]");
                    }
                } else {
                    Log::warning("Auth failed for {$this->endpoint->name}");
                }
            } else {
                $httpClient = $httpClient->withToken($this->endpoint->auth_token);
            }
        }

        try {
            $start = microtime(true);
            $response = $httpClient->send($this->endpoint->method, $this->endpoint->url);
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
                'status_code' => 500,
                'response_time' => null,
                'created_at' => now(),
            ]);
        }

        Cache::put($cacheKey, now()->timestamp);

        EvaluateUptimeJob::dispatch($this->endpoint);
    }
}
