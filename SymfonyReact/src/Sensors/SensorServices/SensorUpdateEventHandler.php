<?php

namespace App\Sensors\SensorServices;

use App\Sensors\Builders\SensorEventUpdateDTOBuilders\SensorEventUpdateDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Events\SensorUpdateEvent;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class SensorUpdateEventHandler
{
    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private SensorEventUpdateDTOBuilder $sensorEventUpdateDTOBuilder,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $elasticLogger,
    ) {}

    /**
     * @param Sensor[] $sensors
     * @return void
     * @throws SensorNotFoundException
     */
    public function handleSensorUpdateEvent(array $sensors): void
    {
        $sensorsToUpdateIDs = [];
        foreach ($sensors as $sensor) {
            /** @var Sensor[] $sensorsToUpdate */
            $sensorsToUpdate = $this->sensorRepository->findSameSensorTypesOnSameDevice(
                $sensor->getDevice()->getDeviceID(),
                $sensor->getSensorTypeObject()->getSensorTypeID(),
            );
        }
        if (empty($sensorsToUpdate)) {
            throw new SensorNotFoundException('No sensors found to update');
        }
        foreach ($sensorsToUpdate as $sensorToUpdate) {
            $sensorsToUpdateIDs[] = $sensorToUpdate->getSensorID();
        }
        $updateSensorEventDTO = $this->sensorEventUpdateDTOBuilder->buildSensorEventUpdateDTO($sensorsToUpdateIDs);

            $sensorUpdateEvent = new SensorUpdateEvent($updateSensorEventDTO);
        $this->eventDispatcher->dispatch($sensorUpdateEvent, SensorUpdateEvent::NAME);

        $this->elasticLogger->info(sprintf('Sensor update event dispatched for sensors: %s', implode(', ', $sensorsToUpdateIDs)));
    }
}
