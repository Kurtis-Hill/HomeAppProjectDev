<?php

namespace App\Sensors\SensorServices\NewSensor;

use App\Sensors\Builders\SensorEventUpdateDTOBuilders\SensorEventUpdateDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Events\SensorUpdateEvent;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class SensorSavingHandler
{
    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private SensorEventUpdateDTOBuilder $sensorEventUpdateDTOBuilder,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function saveSensor(Sensor $sensor): bool
    {
        try {
            $this->sensorRepository->persist($sensor);
            $this->sensorRepository->flush();

            $this->handleSensorUpdateEvent([$sensor]);
            return true;
        } catch (ORMException|OptimisticLockException) {
            return false;
        }
    }

    // doesnt work need to remember to grab related sensors and send full request
    public function saveBulkSensor(array $sensors): bool
    {
        try {
            $sensorsToSend = [];
            foreach ($sensors as $sensor) {
                if (!$sensor instanceof Sensor) {
                    continue;
                }
                $sensorsToSend[] = $sensor;
                $this->sensorRepository->persist($sensor);
            }
            $this->sensorRepository->flush();

            $batchedSensorIDs = array_chunk($sensorsToSend, 100);
            foreach ($batchedSensorIDs as $batchedSensors) {
                $this->handleSensorUpdateEvent($batchedSensors);
            }

            return true;
        } catch (ORMException|OptimisticLockException) {
            return false;
        }
    }

    /**
     * @param Sensor[] $sensors
     * @return void
     * @throws SensorNotFoundException
     */
    private function handleSensorUpdateEvent(array $sensors): void
    {
        $sensorsToUpdateIDs = [];
        foreach ($sensors as $sensor) {
            /** @var Sensor[] $sensorsToUpdate */
            $sensorsToUpdate = $this->sensorRepository->findSameSensorTypesOnSameDevice($sensor);
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

    }
}
