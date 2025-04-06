<?php

use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Endpoint;
use App\Models\User;
use App\Models\EndpointLog;
use App\Jobs\EvaluateUptimeJob;
use App\Notifications\EndpointFailed;
use Carbon\Carbon;

class EvaluateUptimeJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_notification_if_uptime_below_threshold()
    {
        Notification::fake();

        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'user_id' => $user->id,
            'alert_threshold' => 90,
        ]);

        // Simulate logs: 3 failed, 1 success = 25% uptime
        EndpointLog::factory()->count(3)->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => 500,
            'created_at' => now()->subMinutes(10),
        ]);

        EndpointLog::factory()->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => 200,
            'created_at' => now()->subMinutes(5),
        ]);

        EvaluateUptimeJob::dispatchSync($endpoint);

        Notification::assertSentTo($user, EndpointFailed::class);
    }

    public function test_it_does_not_notify_if_uptime_is_ok()
    {
        Notification::fake();

        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'user_id' => $user->id,
            'alert_threshold' => 90,
        ]);

        // All successful logs = 100% uptime
        EndpointLog::factory()->count(5)->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => 200,
            'created_at' => now()->subMinutes(5),
        ]);

        EvaluateUptimeJob::dispatchSync($endpoint);

        Notification::assertNothingSent();
    }

    public function test_it_uses_cache_to_prevent_duplicate_alerts()
    {
        Notification::fake();
        Cache::put("endpoint:1:notified", true, now()->addMinutes(30));

        $user = User::factory()->create();
        $endpoint = Endpoint::factory()->create([
            'id' => 1,
            'user_id' => $user->id,
            'alert_threshold' => 90,
        ]);

        EndpointLog::factory()->count(5)->create([
            'endpoint_id' => $endpoint->id,
            'status_code' => 500,
            'created_at' => now()->subMinutes(5),
        ]);

        EvaluateUptimeJob::dispatchSync($endpoint);

        Notification::assertNothingSent();
    }
}
