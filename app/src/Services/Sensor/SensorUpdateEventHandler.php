<?php

namespace App\Services\Sensor;

use App\Builders\Sensor\Internal\SensorEventUpdateDTOBuilders\SensorEventUpdateDTOBuilder;
use App\Builders\Sensor\Internal\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
use App\Entity\Sensor\Sensor;
use App\Events\Sensor\SensorUpdateEvent;
use App\Exceptions\Sensor\SensorNotFoundException;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class SensorUpdateEventHandler
{
    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private SensorEventUpdateDTOBuilder $sensorEventUpdateDTOBuilder,
        private EventDispatcherInterface $eventDispatcher,
        private SingleSensorUpdateRequestDTOBuilder $singleSensorUpdateRequestDTOBuilder,
        private LoggerInterface $elasticLogger,
    ) {}

    /**
     * @param Sensor[] $sensors
     * @return void
     * @throws SensorNotFoundException
     */
    public function handleSensorUpdateEvent(array $sensors): void
    {
        $sensorsToUpdate = [];
        foreach ($sensors as $sensor) {
            /** @var Sensor[] $sensorsToUpdate */
            $sensorsToUpdate = array_merge(
                $this->sensorRepository->findSameSensorTypesOnSameDevice(
                    $sensor->getDevice()->getDeviceID(),
                    $sensor->getSensorTypeObject()->getSensorTypeID(),
                ),
                $sensorsToUpdate
            );
        }
        if (empty($sensorsToUpdate)) {
            throw new SensorNotFoundException('No sensors found to update');
        }

        $sensorUpdateRequestDTOsByDeviceID = [];
        foreach ($sensorsToUpdate as $sensorToUpdate) {
            $sensorUpdateRequestDTOsByDeviceID[$sensorToUpdate->getDevice()->getDeviceID()][] = $this->singleSensorUpdateRequestDTOBuilder->buildSensorUpdateRequestDTO($sensorToUpdate);
        }

        foreach ($sensorUpdateRequestDTOsByDeviceID as $sensorUpdateRequestDTOs) {
            $updateSensorEventDTO = $this->sensorEventUpdateDTOBuilder->buildSensorEventUpdateDTO($sensorUpdateRequestDTOs);
            $sensorUpdateEvent = new SensorUpdateEvent($updateSensorEventDTO);
            $this->eventDispatcher->dispatch($sensorUpdateEvent, SensorUpdateEvent::NAME);
        }
    }
}
