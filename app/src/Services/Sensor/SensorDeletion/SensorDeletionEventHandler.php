<?php

declare(strict_types=1);

namespace App\Services\Sensor\SensorDeletion;

use App\Builders\Sensor\Internal\SensorEventDTOBuilders\SensorEventDeleteDTOBuilder;
use App\Entity\Sensor\Sensor;
use App\Events\Sensor\SensorDeleteEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class SensorDeletionEventHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private SensorEventDeleteDTOBuilder $sensorEventUpdateDTOBuilder,
    ) {
    }

    public function handleSensorDeletionEvent(string $sensorType, int $deviceID): void
    {
        $sensorDeleteEventDTO = $this->sensorEventUpdateDTOBuilder->buildSensorDeletionEventDTO($sensorType, $deviceID);
        $event = new SensorDeleteEvent($sensorDeleteEventDTO);
        $this->eventDispatcher->dispatch($event, SensorDeleteEvent::NAME);
    }
}
