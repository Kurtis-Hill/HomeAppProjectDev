<?php

namespace App\Sensors\SensorServices\NewSensor;

use App\Sensors\Builders\SensorEventUpdateDTOBuilders\SensorEventUpdateDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Events\SensorUpdateEvent;
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

            $this->handleSensorUpdateEvent([$sensor->getSensorID()]);
            return true;
        } catch (ORMException|OptimisticLockException) {
            return false;
        }
    }

    public function saveBulkSensor(array $sensors): bool
    {
        try {
            $sensorIDs = [];
            foreach ($sensors as $sensor) {
                if (!$sensor instanceof Sensor) {
                    continue;
                }
                $sensorIDs[] = $sensor->getSensorID();
                $this->sensorRepository->persist($sensor);
            }
            $this->sensorRepository->flush();

            $this->handleSensorUpdateEvent($sensorIDs);

            return true;
        } catch (ORMException|OptimisticLockException) {
            return false;
        }
    }

    private function handleSensorUpdateEvent(array $sensorIDs): void
    {
        $batchedSensorIDs = array_chunk($sensorIDs, 100);

        foreach ($batchedSensorIDs as $batchedSensors) {
            $updateSensorEventDTO = $this->sensorEventUpdateDTOBuilder->buildSensorEventUpdateDTO($batchedSensors);
            $sensorUpdateEvent = new SensorUpdateEvent($updateSensorEventDTO);
            $this->eventDispatcher->dispatch($sensorUpdateEvent, SensorUpdateEvent::NAME);
        }
    }
}
