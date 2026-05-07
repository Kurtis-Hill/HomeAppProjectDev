<?php

namespace App\Services\Sensor\NewSensor;

use App\Entity\Sensor\Sensor;
use App\Services\Sensor\UpdateSensor\SensorUpdateEventHandler;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

readonly class SensorSavingHandler
{
    public function __construct(
        private SensorUpdateEventHandler $sensorUpdateEventHandler,
        private EntityManagerInterface $entityManager,
    ) {}

    public function saveSensor(Sensor $sensor): bool
    {
        try {
            $this->entityManager->persist($sensor);
            $this->entityManager->flush();

            $this->sensorUpdateEventHandler->handleSensorUpdateEvent($sensor);
            return true;
        } catch (ORMException|OptimisticLockException) {
            return false;
        }
    }
}
