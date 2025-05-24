<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum MonitoringEventType: string
{
    use EnumToArray;

    case STATUS_CHANGE = 'status_change';
    case RESPONSE_TIME = 'response_time';
    case UNKNOWN = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::STATUS_CHANGE => 'Status Change',
            self::RESPONSE_TIME => 'Response Time',
            self::UNKNOWN => 'Unknown',
        };
    }
}
