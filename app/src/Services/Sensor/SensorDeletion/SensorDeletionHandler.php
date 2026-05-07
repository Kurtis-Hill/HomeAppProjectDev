<?php

namespace App\Services\Sensor\SensorDeletion;

use App\Entity\Sensor\Sensor;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Sensor\UpdateSensor\SensorUpdateEventHandler;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;

readonly class SensorDeletionHandler
{
    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private SensorUpdateEventHandler $sensorUpdateEventHandler,
        private SensorDeletionEventHandler $sensorDeletionEventHandler,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function deleteSensor(Sensor $sensor, bool $triggerESPUpdate = true): bool
    {
        try {
            $deviceID = $sensor->getDevice()->getDeviceID();
            $sensorTypeID = $sensor->getSensorTypeObject()->getSensorTypeID();
            $sensorType = $sensor->getSensorTypeObject()::getSensorTypeName();

            $this->entityManager->remove($sensor);
            $this->entityManager->flush();

            $sameSensorsOnDevice = $this->sensorRepository->findSameSensorTypesOnSameDevice(
                $deviceID,
                $sensorTypeID,
            );
            if (!empty($sameSensorsOnDevice) && $triggerESPUpdate === true) {
                $this->sensorUpdateEventHandler->handleSensorUpdateEvent($sensor);
            }
            elseif (empty($sameSensorsOnDevice) && $triggerESPUpdate === true) {
                $this->sensorDeletionEventHandler->handleSensorDeletionEvent($sensorType, $deviceID);
            }
        } catch (ORMException|OptimisticLockException $e) {
            $this->logger->error('Failed to remove sensor', [$e->getMessage()]);

            return false;
        }

        return true;
    }
}
