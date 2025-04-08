<?php

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Endpoint;
use App\Jobs\CheckEndpointJob;
use App\Models\User;

class CheckEndpointsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_dispatches_jobs_in_batches()
    {
        // Create 30 fake endpoints
        $user = User::factory()->create();
        Endpoint::factory()->count(30)->create([
            'user_id' => $user->id,
        ]);

        Bus::fake();

        Artisan::call('api:check-endpoints');

        // Assert dispatched batches
        Bus::assertBatched(function ($batch) {
            return count($batch->jobs) === 15 &&
                $batch->jobs->first() instanceof CheckEndpointJob;
        });

        $this->assertStringContainsString(
            'API endpoints batch check dispatched successfully!',
            Artisan::output()
        );
    }
}
