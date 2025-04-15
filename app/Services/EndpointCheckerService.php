<?php

namespace App\Services;

use App\Models\Endpoint;
use App\Models\EndpointLog;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class EndpointCheckerService
{
    public function shouldCheck(Endpoint $endpoint)
    {
        $key = "endpoint_check::{$endpoint->id}";
        $lastChecked = Cache::get($key);
        $interval = $endpoint->check_interval * 60;

        return !$lastChecked || (now()->timestamp - $lastChecked) < $interval;
    }

    public function check(Endpoint $endpoint)
    {
        $client = $this->prepareHttpClient($endpoint);
        $start = microtime(true);

        try {
            $response = $client->send($endpoint->method, $endpoint->url);
            $time = round((microtime(true) - $start) * 1000);
            $this->logSuccess($endpoint, $response->status(), $time);
        } catch (\Exception $e) {
            $this->logFailure($endpoint, $e);
        }

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
            $tokenKey = $endpoint->auth_token_name ?? 'token';
            $token = data_get($authResponse->json(), $tokenKey);

            if ($token) {
                $endpoint->auth_token = $token;
                $endpoint->save();
                return $client->withToken($token);
            }

            Log::warning("Token not found using key [{$tokenKey}]");
        } else {
            Log::warning("Auth failed for {$endpoint->name}");
        }

        return $client;
    }

    private function logSuccess(Endpoint $endpoint, int $status, float $time)
    {
        Log::info("Checked {$endpoint->name}: {$status} in {$time}ms");

        EndpointLog::create([
            'endpoint_id' => $endpoint->id,
            'status_code' => $status,
            'response_time' => $time,
            'created_at' => now(),
        ]);
    }

    private function logFailure(Endpoint $endpoint, \Exception $e)
    {
        Log::error("Failed to check {$endpoint->name}: ".$e->getMessage());

        EndpointLog::create([
            'endpoint_id' => $endpoint->id,
            'status_code' => 500,
            'response_time' => null,
            'created_at' => now(),
        ]);
    }
}
