<?php

namespace App\Services\Sensor\NewSensor;

use App\Entity\Sensor\Sensor;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Sensor\UpdateSensor\SensorUpdateEventHandler;
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

            $this->sensorUpdateEventHandler->handleSensorUpdateEvent($sensor);
            return true;
        } catch (ORMException|OptimisticLockException) {
            return false;
        }
    }
}
