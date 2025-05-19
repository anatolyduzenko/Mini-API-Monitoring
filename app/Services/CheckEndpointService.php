<?php

namespace App\Services;

use AnatolyDuzenko\ConfigurablePrometheus\Services\MetricManager;
use App\Models\Endpoint;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckEndpointService
{
    public function __construct(protected MetricManager $metrics) {}

    public function shouldCheck(Endpoint $endpoint)
    {
        $key = "endpoint_check::{$endpoint->id}";
        $lastChecked = Cache::get($key);
        $interval = $endpoint->check_interval * 60;

        return ! $lastChecked || (now()->timestamp - $lastChecked) > $interval;
    }

    public function check(Endpoint $endpoint)
    {
        $client = $this->prepareHttpClient($endpoint);
        $start = microtime(true);

        rescue(function () use ($client, $endpoint, $start) {
            $response = $client->send($endpoint->method, $endpoint->url);
            $time = (microtime(true) - $start) * 1000;
            Log::info("Checked {$endpoint->name}: {$response->status()} in {$time}ms");
            LogsService::logSuccess($endpoint, $response->status(), round($time));
            $this->updateMetrics($endpoint, $response->status(), $time);
        }, function ($e) use ($endpoint) {
            Log::error("Failed to check {$endpoint->name}: ".$e->getMessage());
            LogsService::logFailure($endpoint, $e);
        });

        Cache::put("endpoint_check::{$endpoint->id}", now()->timestamp);
    }

    private function prepareHttpClient(Endpoint $endpoint)
    {
        $client = Http::timeout(10);

        return match ($endpoint->auth_type) {
            'basic' => $client->withBasicAuth($endpoint->username, $endpoint->password),
            'token' => $this->tokenAuthClient($client, $endpoint),
            default => $client,
        };
    }

    private function tokenAuthClient(PendingRequest $client, Endpoint $endpoint)
    {
        if ($endpoint->auth_token) {
            return $client->withToken($endpoint->auth_token);
        }

        $authResponse = Http::asForm()->post($endpoint->auth_url, [
            'username' => $endpoint->username,
            'password' => $endpoint->password,
        ]);

        if ($authResponse->successful()) {
            $client = $this->checkResponse($client, $endpoint, $authResponse);
        } else {
            Log::warning("Auth failed for {$endpoint->name}");
        }

        return $client;
    }

    private function checkResponse(PendingRequest $client, Endpoint $endpoint, $authResponse)
    {
        $tokenKey = $endpoint->auth_token_name ?? 'token';
        $token = data_get($authResponse->json(), $tokenKey);

        if (! $token) {
            Log::warning("Token not found using key [{$tokenKey}]");

            return $client;
        }

        $endpoint->auth_token = $token;
        $endpoint->save();

        return $client->withToken($token);
    }

    private function updateMetrics($endpoint, $status, $time)
    {
        $this->metrics->observe('endpoints', 'response_time_seconds', $time, [$endpoint->method, $status]);
        $this->metrics->inc('endpoints', 'response_codes', [(string) $status]);
        $this->metrics->inc('endpoints', 'checks_total', ['total']);
    }
}
