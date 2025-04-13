<?php

namespace Tests\Feature;

use App\Enums\SplitType;
use App\Enums\StatusCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SplitTypesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_split_types()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.splitTypes.index'));

        $response->assertStatus(StatusCode::OK->value);
        $response->assertJson([
            [
                'id' => SplitType::DAILY->value,
                'name' => SplitType::DAILY->label(),
            ],
            [
                'id' => SplitType::HOURLY->value,
                'name' => SplitType::HOURLY->label(),
            ],
            [
                'id' => SplitType::DECAMIN->value,
                'name' => SplitType::DECAMIN->label(),
            ],
        ]);
    }
}
