<?php

namespace Tests\Feature;

use App\Enums\SplitTypes;
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
            ],
        ]);
    }
}
