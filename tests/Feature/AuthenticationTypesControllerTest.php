<?php

namespace Tests\Feature;

use App\Enums\AuthenticationType;
use App\Enums\StatusCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTypesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_authentication_types()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.authTypes.index'));

        $response->assertStatus(StatusCode::OK->value);
        $response->assertJson([
            [
                'id' => AuthenticationType::NONE->value,
                'name' => AuthenticationType::NONE->label(),
            ],
            [
                'id' => AuthenticationType::BASIC->value,
                'name' => AuthenticationType::BASIC->label(),
            ],
        ]);
    }
}
