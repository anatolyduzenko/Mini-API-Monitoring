<?php

namespace Tests\Unit;

use App\Enums\SplitTypes;
use PHPUnit\Framework\TestCase;

class SplitTypesTest extends TestCase
{
    public function test_split_types_values()
    {
        $this->assertEquals('daily', SplitTypes::DAILY->value);
        $this->assertEquals('hourly', SplitTypes::HOURLY->value);
        $this->assertEquals('decamin', SplitTypes::DECAMIN->value);
    }

    public function test_from_code()
    {
        $type = SplitTypes::fromCode('daily');
        $this->assertSame(SplitTypes::DAILY, $type);
    }

    public function test_try_from_value()
    {
        $type = SplitTypes::tryFrom('monthly');
        $this->assertNull($type);
    }

    public function test_as_labels()
    {
        $expected = [
            ['id' => 'daily', 'name' => 'Daily'],
            ['id' => 'hourly', 'name' => 'Hourly'],
            ['id' => 'decamin', 'name' => '10 Minutes'],
        ];

        $this->assertEquals($expected, SplitTypes::asLabels());
    }
}
