<?php

namespace App\Services\Sensor\UpdateSensor;

use App\Builders\Sensor\Internal\SensorEventDTOBuilders\SensorEventUpdateDTOBuilder;
use App\Builders\Sensor\Internal\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
use App\Entity\Sensor\Sensor;
use App\Events\Sensor\SensorUpdateEvent;
use App\Exceptions\Sensor\SensorNotFoundException;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Sensor\SensorDeletion\SensorDeletionEventHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class SensorUpdateEventHandler
{
    public function __construct(
        private SensorEventUpdateDTOBuilder $sensorEventUpdateDTOBuilder,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function handleSensorUpdateEvent(Sensor $sensor): void
    {
        $updateSensorEventDTO = $this->sensorEventUpdateDTOBuilder->buildSensorUpdateEventDTO($sensor->getSensorID());
        $sensorUpdateEvent = new SensorUpdateEvent($updateSensorEventDTO);
        $this->eventDispatcher->dispatch($sensorUpdateEvent, SensorUpdateEvent::NAME);
    }
}
