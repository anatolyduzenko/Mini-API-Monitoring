<?php

namespace Tests\Feature;

use App\Enums\MonitoringEventType;
use App\Listeners\Monitoring\Interfaces\MonitoringDTOInterface;
use App\Listeners\Monitoring\Interfaces\MonitoringHandlerInterface;
use App\Services\MonitoringDispatcherService;
use Tests\TestCase;

class MonitoringDispatcherServiceTest extends TestCase
{
    public function test_it_calls_correct_handler()
    {
        $dto = $this->createMock(MonitoringDTOInterface::class);
        $dto->method('getType')->willReturn(MonitoringEventType::STATUS_CHANGE);

        $handler = $this->createMock(MonitoringHandlerInterface::class);
        $handler->expects($this->once())
            ->method('supports')
            ->with($dto)
            ->willReturn(true);

        $handler->expects($this->once())
            ->method('handle')
            ->with($dto);

        $service = new MonitoringDispatcherService([$handler]);
        $service->handle($dto);
    }

    public function test_it_throws_exception_when_no_handler_supports_dto()
    {
        $this->expectException(\RuntimeException::class);

        $dto = $this->createMock(MonitoringDTOInterface::class);
        $dto->method('getType')->willReturn(MonitoringEventType::UNKNOWN);

        $handler = $this->createMock(MonitoringHandlerInterface::class);
        $handler->method('supports')->with($dto)->willReturn(false);

        $service = new MonitoringDispatcherService([$handler]);
        $service->handle($dto);
    }
}
