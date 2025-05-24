<?php

namespace App\Listeners\Monitoring\Interfaces;

interface MonitoringHandlerInterface
{
    public function supports(MonitoringDTOInterface $monitoringDTOInterface): bool;

    public function handle(MonitoringDTOInterface $monitoringDTOInterface): void;
}
