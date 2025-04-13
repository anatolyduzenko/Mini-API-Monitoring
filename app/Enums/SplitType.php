<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum SplitType: string
{
    use EnumToArray;
    case DAILY = 'daily';
    case HOURLY = 'hourly';
    case DECAMIN = 'decamin';

    public function label(): string
    {
        return match ($this) {
            self::DAILY => 'Daily',
            self::HOURLY => 'Hourly',
            self::DECAMIN => '10 Minutes',
        };
    }
}
