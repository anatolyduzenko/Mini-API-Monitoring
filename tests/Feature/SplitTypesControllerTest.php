<?php

namespace Tests\Feature;

use App\Enums\SplitTypes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SplitTypesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_split_types()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson("/api/split-types");

        $response->assertStatus(200);
        $response->assertJson([
            [
                'id' => SplitTypes::DAILY->value,
                'name' => SplitTypes::DAILY->label(),
            ],
            [
                'id' => SplitTypes::HOURLY->value,
                'name' => SplitTypes::HOURLY->label(),
            ],
            [
                'id' => SplitTypes::DECAMIN->value,
                'name' => SplitTypes::DECAMIN->label(),
            ]
        ]);
    }
}
