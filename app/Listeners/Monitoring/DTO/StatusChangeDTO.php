<?php

namespace App\Listeners\Monitoring\DTO;

use App\Enums\MonitoringEventType;
use App\Listeners\Monitoring\Interfaces\MonitoringDTOInterface;
use App\Models\Endpoint;

class StatusChangeDTO implements MonitoringDTOInterface
{
    public function __construct(
        public Endpoint $endpoint,
        public int $currentStatus
    ) {}

    public function getType(): MonitoringEventType
    {
        return MonitoringEventType::STATUS_CHANGE;
    }
}
