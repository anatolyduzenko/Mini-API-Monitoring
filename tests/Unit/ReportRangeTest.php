<?php

namespace Tests\Unit;

use App\Enums\ReportRange;
use PHPUnit\Framework\TestCase;

class ReportRangeTest extends TestCase
{
    public function test_report_range_values()
    {
        $this->assertEquals(1, ReportRange::DAY->value);
        $this->assertEquals(7, ReportRange::WEEK->value);
        $this->assertEquals(90, ReportRange::QUART->value);
    }

    public function test_from_code()
    {
        $range = ReportRange::fromCode(7);
        $this->assertSame(ReportRange::WEEK, $range);
    }

    public function test_try_from_value()
    {
        $status = ReportRange::tryFrom(45);
        $this->assertNull($status);
    }

    public function test_as_labels()
    {
        $expected = [
            ['id' => 1, 'name' => '1 Day'],
            ['id' => 3, 'name' => '3 Days'],
            ['id' => 7, 'name' => '1 Week'],
            ['id' => 30, 'name' => '30 Days'],
            ['id' => 90, 'name' => '90 Days'],
            ['id' => 365, 'name' => '1 Year'],
        ];

        $this->assertEquals($expected, ReportRange::asLabels());
    }
}
