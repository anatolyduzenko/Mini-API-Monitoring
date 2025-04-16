<?php

namespace App\Traits;

use App\Enums\SplitType;

trait DateFormatter
{
    private function dateFormatter(SplitType $splitType)
    {
        return match ($splitType) {
            SplitType::DAILY => 'DATE(endpoint_logs.created_at) as date',
            SplitType::HOURLY => 'DATE_FORMAT(endpoint_logs.created_at, \'%Y-%m-%d %H:00:00\') as date',
            SplitType::DECAMIN => 'DATE_FORMAT(endpoint_logs.created_at, \'%Y-%m-%d %H:%i:00\') as date',
        };
    }
}
