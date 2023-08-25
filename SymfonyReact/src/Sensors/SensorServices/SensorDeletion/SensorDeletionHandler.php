<?php

namespace App\Sensors\SensorServices\SensorDeletion;

use App\Sensors\Builders\SensorEventUpdateDTOBuilders\SensorEventUpdateDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Events\SensorUpdateEvent;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SensorDeletionHandler implements SensorDeletionInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorEventUpdateDTOBuilder $sensorEventUpdateDTOBuilder;

    private EventDispatcherInterface $eventDispatcher;

    private LoggerInterface $logger;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorEventUpdateDTOBuilder $sensorEventUpdateDTOBuilder,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
    )
    {
        $this->sensorEventUpdateDTOBuilder = $sensorEventUpdateDTOBuilder;
        $this->eventDispatcher = $eventDispatcher;
        $this->sensorRepository = $sensorRepository;
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
                $sensorIDs = array_map(
                    static function (Sensor $sensor) {
                        return $sensor->getSensorID();
                    },
                    $sameSensorsOnDevice
                );

                $updateSensorEventDTO = $this->sensorEventUpdateDTOBuilder->buildSensorEventUpdateDTO($sensorsToUpdateIDs);
                $sensorUpdateEvent = new SensorUpdateEvent($updateSensorEventDTO);
                $this->eventDispatcher->dispatch($sensorUpdateEvent, SensorUpdateEvent::NAME);
            }
            //@TODO dispath message to remove the json file


        } catch (ORMException|OptimisticLockException $e) {
            $this->logger->error('Failed to remove sensor', [$e->getMessage()]);

            return false;
        }

        return true;
    }
}
