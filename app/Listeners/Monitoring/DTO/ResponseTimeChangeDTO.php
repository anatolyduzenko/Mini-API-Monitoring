<?php

namespace App\Listeners\Monitoring\DTO;

use App\Enums\MonitoringEventType;
use App\Listeners\Monitoring\Interfaces\MonitoringDTOInterface;
use App\Models\Endpoint;

class ResponseTimeChangeDTO implements MonitoringDTOInterface
{
    public function __construct(
        public Endpoint $endpoint,
        public float $newResponseTime
    ) {}

    public function getType(): MonitoringEventType
    {
        return MonitoringEventType::RESPONSE_TIME;
    }
}
