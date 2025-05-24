<?php

namespace App\Services;

use App\Listeners\Monitoring\Interfaces\MonitoringDTOInterface;
use App\Listeners\Monitoring\Interfaces\MonitoringHandlerInterface;

class MonitoringDispatcherService implements MonitoringHandlerInterface
{
    public function __construct(
        protected iterable $handlers
    ) {}

    public function supports(MonitoringDTOInterface $dto): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($dto)) {
                return true;
            }
        }

        return false;
    }

    public function handle(MonitoringDTOInterface $dto): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($dto)) {
                $handler->handle($dto);

                return;
            }
        }

        throw new \RuntimeException('No handler found for DTO: '.get_class($dto));
    }
}
