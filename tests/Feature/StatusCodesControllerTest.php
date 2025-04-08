<?php

namespace Tests\Feature;

use App\Enums\StatusCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusCodesControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_status_codes()
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.statusCodes.index'));

        $response->assertStatus(StatusCode::OK->value);
        $response->assertJson([
            [
                'id' => StatusCode::OK->value,
                'name' => StatusCode::OK->label(),
            ],
            [
                'id' => StatusCode::CREATED->value,
                'name' => StatusCode::CREATED->label(),
            ],
            [
                'id' => StatusCode::NO_CONTENT->value,
                'name' => StatusCode::NO_CONTENT->label(),
            ],
        ]);
    }
}
