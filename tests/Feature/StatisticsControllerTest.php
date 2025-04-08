<?php

use App\Enums\StatusCode;
use App\Services\LogsService;
use App\Services\StatisticsService;
use Tests\TestCase;

class StatisticsControllerTest extends TestCase
{
    public function test_get_uptime_returns_data()
    {
        $mockData = ['uptime' => 99.9];
        $mockService = $this->createMock(StatisticsService::class);
        $mockService->method('reportUptime')->willReturn($mockData);

        $this->app->instance(StatisticsService::class, $mockService);

        $response = $this->getJson(
            route(
                'api.statistics.uptime',
                [
                    'per_page' => 5,
                ]
            )
        );
        $response->assertStatus(StatusCode::OK->value)
            ->assertJson($mockData);
    }

    public function test_get_recent_logs_returns_data()
    {
        $mockData = [['message' => 'OK']];
        $mockLogs = $this->createMock(LogsService::class);
        $mockLogs->method('recentLogs')->willReturn($mockData);

        $this->app->instance(LogsService::class, $mockLogs);

        $response = $this->getJson(
            route('api.statistics.recent')
        );
        $response->assertStatus(StatusCode::OK->value)
            ->assertJson($mockData);
    }

    public function test_get_uptime_graph_returns_transformed_data()
    {
        $mockTrendData = collect([
            (object) [
                'date' => '2024-04-01',
                'name' => 'API 1',
                'successful_checks' => 9,
                'total_checks' => 10,
            ],
            (object) [
                'date' => '2024-04-01',
                'name' => 'API 2',
                'successful_checks' => 7,
                'total_checks' => 10,
            ],
        ]);

        $mockService = $this->createMock(StatisticsService::class);
        $mockService->method('uptimeTrendData')->willReturn($mockTrendData);
        $this->app->instance(StatisticsService::class, $mockService);

        $response = $this->getJson(
            route('api.statistics.uptimeGraph',
                [
                    'days' => 1,
                    'split_type' => 'daily',
                ]
            )
        );

        $response->assertStatus(StatusCode::OK->value)
            ->assertJsonStructure([
                'labels',
                'graphData' => [['date', 'API 1', 'API 2']],
            ]);
    }

    public function test_get_response_time_returns_transformed_data()
    {
        $mockResponseTime = collect([
            (object) [
                'date' => '2024-04-01',
                'name' => 'API 1',
                'response_time' => 123.456,
            ],
            (object) [
                'date' => '2024-04-01',
                'name' => 'API 2',
                'response_time' => 88.888,
            ],
        ]);

        $mockLogs = $this->createMock(LogsService::class);
        $mockLogs->method('responseTime')->willReturn($mockResponseTime);
        $this->app->instance(LogsService::class, $mockLogs);

        $response = $this->getJson(
            route('api.statistics.responseTime', ['days' => 1])
        );

        $response->assertStatus(StatusCode::OK->value)
            ->assertJsonStructure([
                'labels',
                'graphData' => [['date', 'API 1', 'API 2']],
            ]);
    }
}
