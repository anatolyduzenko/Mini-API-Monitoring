<?php

namespace Tests\Unit;

use App\Enums\SplitType;
use PHPUnit\Framework\TestCase;

class SplitTypesTest extends TestCase
{
    public function test_split_types_values()
    {
        $this->assertEquals('daily', SplitType::DAILY->value);
        $this->assertEquals('hourly', SplitType::HOURLY->value);
        $this->assertEquals('decamin', SplitType::DECAMIN->value);
    }

    public function test_from_code()
    {
        $type = SplitType::fromCode('daily');
        $this->assertSame(SplitType::DAILY, $type);
    }

    public function test_try_from_value()
    {
        $type = SplitType::tryFrom('monthly');
        $this->assertNull($type);
    }

    public function test_as_labels()
    {
        $expected = [
            ['id' => 'daily', 'name' => 'Daily'],
            ['id' => 'hourly', 'name' => 'Hourly'],
            ['id' => 'decamin', 'name' => '10 Minutes'],
        ];

        $this->assertEquals($expected, SplitType::asLabels());
    }
}
