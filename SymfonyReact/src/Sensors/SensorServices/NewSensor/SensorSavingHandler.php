<?php

namespace App\Sensors\SensorServices\NewSensor;

use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\SensorUpdateEventHandler;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

readonly class SensorSavingHandler
{
    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private SensorUpdateEventHandler $sensorUpdateEventHandler,
    ) {}

    public function saveSensor(Sensor $sensor): bool
    {
        try {
            $this->sensorRepository->persist($sensor);
            $this->sensorRepository->flush();

            $this->sensorUpdateEventHandler->handleSensorUpdateEvent([$sensor]);
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
                $this->sensorUpdateEventHandler->handleSensorUpdateEvent($batchedSensors);
            }

            return true;
        } catch (ORMException|OptimisticLockException) {
            return false;
        }
    }
}
