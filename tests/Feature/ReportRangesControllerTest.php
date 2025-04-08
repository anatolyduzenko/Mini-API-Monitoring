<?php

namespace Tests\Feature;

use App\Enums\ReportRange;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReportRangesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_report_ranges()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson("/api/report-ranges");

        $response->assertStatus(200);
        $response->assertJson([
            [
                'id' => ReportRange::DAY->value,
                'name' => ReportRange::DAY->label(),
            ]
        ]);
    }
}
