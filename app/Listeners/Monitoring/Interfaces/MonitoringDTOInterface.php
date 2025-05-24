<?php

namespace App\Listeners\Monitoring\Interfaces;

use App\Enums\MonitoringEventType;

interface MonitoringDTOInterface
{
    public function getType(): MonitoringEventType;
}
