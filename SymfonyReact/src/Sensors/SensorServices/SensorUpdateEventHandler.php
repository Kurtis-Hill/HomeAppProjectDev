<?php

namespace App\Sensors\SensorServices;

use App\Sensors\Builders\Internal\SensorEventUpdateDTOBuilders\SensorEventUpdateDTOBuilder;
use App\Sensors\Builders\Internal\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
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
