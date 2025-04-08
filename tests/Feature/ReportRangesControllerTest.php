<?php

namespace Tests\Feature;

use App\Enums\ReportRange;
use App\Enums\StatusCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportRangesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_report_ranges()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.reportRanges.index'));

        $response->assertStatus(StatusCode::OK->value);
        $response->assertJson([
            [
                'id' => ReportRange::DAY->value,
                'name' => ReportRange::DAY->label(),
            ],
        ]);
    }
}
