<?php

namespace Tests\Unit;

use App\Enums\SplitType;
use App\Enums\StatusCode;
use App\Models\Endpoint;
use App\Models\EndpointLog;
use App\Models\User;
use App\Services\StatisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StatisticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StatisticsService;
    }

    public function test_report_uptime_returns_paginated_results()
    {
        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'name' => 'API A',
            'user_id' => $user->id,
            'dashboard_visible' => 1,
        ]);
        EndpointLog::factory()->count(3)->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => StatusCode::OK->value,
        ]);

        EndpointLog::factory()->count(2)->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => StatusCode::INTERNAL_SERVER_ERROR->value,
        ]);

        $result = $this->service->reportUptime(10);

        $this->assertEquals(1, $result->total());
        $data = $result->items()[0];
        $this->assertEquals('API A', $data['name']);
        $this->assertEquals(60.0, $data['uptime']); // 3 out of 5 successful
    }

    public function test_uptime_trend_data_returns_grouped_data()
    {
        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'name' => 'API Graph',
            'user_id' => $user->id,
            'dashboard_visible' => 1,
        ]);

        EndpointLog::factory()->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => StatusCode::OK->value,
            'created_at' => now()->subDays(1),
        ]);

        EndpointLog::factory()->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => StatusCode::INTERNAL_SERVER_ERROR->value,
            'created_at' => now()->subDays(1),
        ]);

        $result = $this->service->uptimeTrendData(2, SplitType::DAILY);

        $this->assertNotEmpty($result);
        $row = $result->first();
        $this->assertEquals('API Graph', $row->name);
        $this->assertEquals(1, $row->successful_checks);
        $this->assertEquals(2, $row->total_checks);
    }
}
