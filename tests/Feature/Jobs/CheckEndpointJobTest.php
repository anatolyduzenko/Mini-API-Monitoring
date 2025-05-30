<?php

namespace Tests\Feature\Jobs;

use AnatolyDuzenko\ConfigurablePrometheus\Contracts\MetricManagerInterface;
use App\Jobs\CheckEndpointJob;
use App\Models\Endpoint;
use App\Models\User;
use App\Services\CheckEndpointService;
use App\Services\LogsService;
use App\Services\MonitoringDispatcherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class CheckEndpointJobTest extends TestCase
{
    use RefreshDatabase;

    protected $metricsMock;

    protected $transitionMock;

    protected $logsMock;

    protected CheckEndpointService $service;

    protected function setUp(): void
    {
        parent::setUp();

        if (! defined('LARAVEL_START')) {
            define('LARAVEL_START', microtime(true) - 0.5);
        }

        $this->metricsMock = Mockery::mock(MetricManagerInterface::class);
        $this->transitionMock = Mockery::mock(MonitoringDispatcherService::class);
        $this->logsMock = Mockery::mock(LogsService::class);

        $this->service = new CheckEndpointService(
            $this->metricsMock,
            $this->transitionMock,
            $this->logsMock
        );

        $this->metricsMock->shouldReceive('observe');
        $this->metricsMock->shouldReceive('inc');
    }

    public function test_it_logs_successful_response()
    {
        Cache::flush();

        Http::fake([
            'https://example.com' => Http::response([], 200),
        ]);

        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'method' => 'GET',
            'check_interval' => 1,
        ]);

        $this->app->instance(MetricManagerInterface::class, $this->metricsMock);
        $this->app->instance(CheckEndpointService::class, $this->service);
        $this->logsMock->shouldReceive('logSuccess')->andReturnNull();
        $this->transitionMock->shouldReceive('handleMany')->once();

        CheckEndpointJob::dispatchSync($endpoint);
    }

    public function test_it_logs_failure_when_request_fails()
    {
        Cache::flush();

        $this->app->bind(\App\Services\MonitoringDispatcherService::class, function ($app) {
            return new \App\Services\MonitoringDispatcherService([
                $app->make(\App\Listeners\Monitoring\StatusChangeHandler::class),
            ]);
        });

        Http::fake([
            'https://broken.com' => Http::sequence()->pushStatus(500),
        ]);

        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://broken.com',
        ]);

        CheckEndpointJob::dispatchSync($endpoint);

        $this->assertDatabaseHas('endpoint_logs', [
            'endpoint_id' => $endpoint->id,
            'status_code' => 500,
        ]);
    }
}
