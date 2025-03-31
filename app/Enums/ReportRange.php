<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum ReportRange: int
{
    use EnumToArray;
    case DAY = 1;
    case THREEDAYS = 3;
    case WEEK = 7;
    case MONTH = 30;
    case QUART = 90;
    case YEAR = 365;

    public function label(): string
    {
        return match ($this) {
            self::DAY => '1 Day',
            self::THREEDAYS => '3 Days',
            self::WEEK => '1 Week',
            self::MONTH => '30 Days',
            self::QUART => '90 Days',
            self::YEAR => '1 Year',
        };
    }
}
