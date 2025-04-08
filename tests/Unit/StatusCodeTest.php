<?php

namespace Tests\Unit;

use App\Enums\StatusCode;
use PHPUnit\Framework\TestCase;

class StatusCodeTest extends TestCase
{
    public function test_status_code_values()
    {
        $this->assertEquals(200, StatusCode::OK->value);
        $this->assertEquals(400, StatusCode::BAD_REQUEST->value);
        $this->assertEquals(500, StatusCode::INTERNAL_SERVER_ERROR->value);
    }

    public function test_from_code()
    {
        $range = StatusCode::fromCode(503);
        $this->assertSame(StatusCode::SERVICE_UNAVAILABLE, $range);
    }

    public function test_try_from_value()
    {
        $status = StatusCode::tryFrom(419);
        $this->assertNull($status);
    }

    public function test_as_labels()
    {
        $expected = [
            ['id' => 200, 'name' => 'OK'],
            ['id' => 201, 'name' => 'Created'],
            ['id' => 204, 'name' => 'No Content'],
            ['id' => 400, 'name' => 'Bad Request'],
            ['id' => 401, 'name' => 'Unauthorized'],
            ['id' => 403, 'name' => 'Forbidden'],
            ['id' => 404, 'name' => 'Not Found'],
            ['id' => 500, 'name' => 'Internal Server Error'],
            ['id' => 503, 'name' => 'Service Unavailable'],
        ];

        $this->assertEquals($expected, StatusCode::asLabels());
    }
}
