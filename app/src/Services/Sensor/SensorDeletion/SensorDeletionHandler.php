<?php

namespace App\Services\Sensor\SensorDeletion;

use App\Entity\Sensor\Sensor;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Sensor\UpdateSensor\SensorUpdateEventHandler;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;

readonly class SensorDeletionHandler implements SensorDeletionInterface
{
    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private SensorUpdateEventHandler $sensorUpdateEventHandler,
        private SensorDeletionEventHandler $sensorDeletionEventHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function deleteSensor(Sensor $sensor): bool
    {
        try {
            $deviceID = $sensor->getDevice()->getDeviceID();
            $sensorTypeID = $sensor->getSensorTypeObject()->getSensorTypeID();
            $sensorType = $sensor->getSensorTypeObject()::getSensorTypeName();

            $this->sensorRepository->remove($sensor);
            $this->sensorRepository->flush();

            $sameSensorsOnDevice = $this->sensorRepository->findSameSensorTypesOnSameDevice(
                $deviceID,
                $sensorTypeID,
            );
            if (!empty($sameSensorsOnDevice)) {
                $this->sensorUpdateEventHandler->handleSensorUpdateEvent($sameSensorsOnDevice);
            }
            else {
                $this->sensorDeletionEventHandler->handleSensorDeletionEvent($sensorType, $deviceID);
            }
        } catch (ORMException|OptimisticLockException $e) {
            $this->logger->error('Failed to remove sensor', [$e->getMessage()]);

            return false;
        }

        return true;
    }
}
