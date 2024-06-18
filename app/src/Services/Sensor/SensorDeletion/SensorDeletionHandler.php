<?php

namespace App\Services\Sensor\SensorDeletion;

use App\Entity\Sensor\Sensor;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Sensor\SensorUpdateEventHandler;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;

class SensorDeletionHandler implements SensorDeletionInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorUpdateEventHandler $sensorUpdateEventHandler;

    private LoggerInterface $logger;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorUpdateEventHandler $sensorUpdateEventHandler,
        LoggerInterface $logger,
    )
    {
        $this->sensorRepository = $sensorRepository;
        $this->sensorUpdateEventHandler = $sensorUpdateEventHandler;
        $this->logger = $logger;
    }

    public function deleteSensor(Sensor $sensor): bool
    {
        try {
            $deviceID = $sensor->getDevice()->getDeviceID();
            $sensorTypeID = $sensor->getSensorTypeObject()->getSensorTypeID();

            $this->sensorRepository->remove($sensor);
            $this->sensorRepository->flush();

            $sameSensorsOnDevice = $this->sensorRepository->findSameSensorTypesOnSameDevice(
                $deviceID,
                $sensorTypeID,
            );
            if (!empty($sameSensorsOnDevice)) {
                $this->sensorUpdateEventHandler->handleSensorUpdateEvent($sameSensorsOnDevice);
            }
            //@TODO dispath message to remove the json file


        } catch (ORMException|OptimisticLockException $e) {
            $this->logger->error('Failed to remove sensor', [$e->getMessage()]);

            return false;
        }

        return true;
    }
}
