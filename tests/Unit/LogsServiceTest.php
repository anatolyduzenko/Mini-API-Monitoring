<?php

namespace Tests\Unit;

use App\Enums\SplitType;
use App\Enums\StatusCode;
use App\Models\Endpoint;
use App\Models\EndpointLog;
use App\Models\User;
use App\Services\LogsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LogsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LogsService;
    }

    public function test_recent_logs_returns_expected_structure()
    {
        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'name' => 'Test API',
            'user_id' => $user->id,
            'dashboard_visible' => 1,
        ]);
        EndpointLog::factory()->count(3)->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => StatusCode::OK->value,
            'response_time' => 123,
        ]);

        $result = $this->service->recentLogs();

        $this->assertCount(3, $result);
        $this->assertEquals('Test API', $result->first()->name);
        $this->assertObjectHasProperty('status_code', $result->first());
    }

    public function test_response_time_returns_grouped_data()
    {
        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'name' => 'Grouped API',
            'user_id' => $user->id,
            'dashboard_visible' => 1,
        ]);
        EndpointLog::factory()->count(3)->create([
            'endpoint_id' => $endpoint->id,
            'response_time' => 100,
            'created_at' => now()->subDays(1),
        ]);

        $result = $this->service->responseTime(7, SplitType::DAILY);

        $this->assertNotEmpty($result);
        $this->assertObjectHasProperty('date', $result->first());
        $this->assertEquals('Grouped API', $result->first()->name);
        $this->assertEquals(100.0, round($result->first()->response_time, 1));
    }
}
