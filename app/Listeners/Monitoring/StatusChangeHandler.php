<?php

namespace App\Listeners\Monitoring;

use App\Events\EndpointFailure;
use App\Events\EndpointRecovered;
use App\Listeners\Monitoring\DTO\StatusChangeDTO;
use App\Listeners\Monitoring\Interfaces\MonitoringDTOInterface;
use App\Listeners\Monitoring\Interfaces\MonitoringHandlerInterface;
use Illuminate\Support\Facades\Cache;

class StatusChangeHandler implements MonitoringHandlerInterface
{
    protected int $threshold = 5;

    /**
     * Create the event listener.
     */
    public function supports(MonitoringDTOInterface $monitoringDTOInterface): bool
    {
        return $monitoringDTOInterface instanceof StatusChangeDTO;
    }

    /**
     * Handle the event.
     */
    public function handle(MonitoringDTOInterface $monitoringDTOInterface): void
    {
        $key = "endpoint:{$monitoringDTOInterface->endpoint->id}";
        $data = Cache::get($key, [
            'last_status' => 200,
            'failure_count' => 0,
            'notified' => false,
        ]);

        $lastStatus = $data['last_status'];
        $failureCount = $data['failure_count'];
        $notified = $data['notified'];

        if ($monitoringDTOInterface->currentStatus >= 400) {
            $failureCount++;

            if ($failureCount >= $this->threshold && ! $notified) {
                broadcast(new EndpointFailure($monitoringDTOInterface->endpoint, $monitoringDTOInterface->currentStatus));

                $this->updateCache($key, $monitoringDTOInterface->currentStatus, $failureCount, true);
            } else {
                $this->updateCache($key, $monitoringDTOInterface->currentStatus, $failureCount, $notified);
            }

        } else {
            if ($failureCount >= $this->threshold && $notified) {
                // Recovered
                broadcast(new EndpointRecovered($monitoringDTOInterface->endpoint, $monitoringDTOInterface->currentStatus));
            }

            $this->updateCache($key, $monitoringDTOInterface->currentStatus, 0, false);
        }
    }

    protected function updateCache(string $key, int $status, int $failures, bool $notified): void
    {
        Cache::put($key, [
            'last_status' => $status,
            'failure_count' => $failures,
            'notified' => $notified,
        ]);
    }
}
