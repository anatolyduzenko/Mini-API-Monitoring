<?php

namespace App\Listeners\Monitoring;

use App\Events\ResponseTimeChanged;
use App\Listeners\Monitoring\DTO\ResponseTimeChangeDTO;
use App\Listeners\Monitoring\Interfaces\MonitoringDTOInterface;
use App\Listeners\Monitoring\Interfaces\MonitoringHandlerInterface;
use Illuminate\Support\Facades\Cache;

class ResponseTimeChangeHandler implements MonitoringHandlerInterface
{
    protected float $threshold = 5.0;

    /**
     * Check input data type.
     */
    public function supports(MonitoringDTOInterface $monitoringDTOInterface): bool
    {
        return $monitoringDTOInterface instanceof ResponseTimeChangeDTO;
    }

    /**
     * Handle the event.
     */
    public function handle(MonitoringDTOInterface $monitoringDTOInterface): void
    {
        $key = "endpoint:response_time:{$monitoringDTOInterface->endpoint->id}";
        $data = Cache::get($key, [
            'response_time' => 0,
            'notified' => false,
        ]);

        $previousResponseTime = $data['response_time'];
        $notified = $data['notified'];
        if (abs($previousResponseTime - $monitoringDTOInterface->newResponseTime) > $this->threshold) {
            broadcast(new ResponseTimeChanged($monitoringDTOInterface->endpoint, $monitoringDTOInterface->newResponseTime));
            $this->updateCache($key, $monitoringDTOInterface->newResponseTime, $notified);
        } else {
            $this->updateCache($key, $monitoringDTOInterface->newResponseTime, false);
        }

    }

    protected function updateCache(string $key, int $responseTime, bool $notified): void
    {
        Cache::put($key, [
            'response_time' => $responseTime,
            'notified' => $notified,
        ]);
    }
}
