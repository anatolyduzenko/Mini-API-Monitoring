<?php

namespace Tests\Feature;

use App\Enums\RequestType;
use App\Enums\StatusCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestTypesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_request_types()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.requestTypes.index'));

        $response->assertStatus(StatusCode::OK->value);
        $response->assertJson([
            [
                'id' => RequestType::GET->value,
                'name' => RequestType::GET->label(),
            ],
        ]);
    }
}
