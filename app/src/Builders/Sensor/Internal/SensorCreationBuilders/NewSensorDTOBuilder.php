<?php

namespace App\Builders\Sensor\Internal\SensorCreationBuilders;

use App\DTOs\Sensor\Internal\Sensor\NewSensorDTO;
use App\Entity\Device\Devices;
use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use App\Exceptions\Sensor\DeviceNotFoundException;
use App\Exceptions\Sensor\SensorTypeNotFoundException;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Repository\Sensor\Sensors\SensorTypeRepositoryInterface;

readonly class NewSensorDTOBuilder
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private SensorTypeRepositoryInterface $sensorTypeRepository,
    ) {}

    /**
     * @throws \App\Exceptions\Sensor\SensorTypeNotFoundException
     * @throws \App\Exceptions\Sensor\DeviceNotFoundException
     */
    public function buildNewSensorDTO(
        string $sensorName,
        int $sensorTypeID,
        int $deviceID,
        User $user,
        int $pinNumber,
        ?int $readingInterval,
    ): NewSensorDTO {
        $newSensor = new Sensor();

        $deviceObject = $this->deviceRepository->find($deviceID);
        if (!$deviceObject instanceof Devices) {
            throw new DeviceNotFoundException(
                sprintf(
                    DeviceNotFoundException::DEVICE_NOT_FOUND_FOR_ID,
                    $deviceID,
                )
            );
        }

        $sensorTypeObject = $this->sensorTypeRepository->find($sensorTypeID);
        if (!$sensorTypeObject instanceof AbstractSensorType) {
            throw new SensorTypeNotFoundException(
                sprintf(
                    SensorTypeNotFoundException::SENSOR_TYPE_NOT_FOUND_FOR_ID,
                    $sensorTypeID,
                )
            );
        }

        return new NewSensorDTO(
            $sensorName,
            $sensorTypeObject,
            $deviceObject,
            $user,
            $newSensor,
            $pinNumber,
            $readingInterval ?? Sensor::DEFAULT_READING_INTERVAL,
        );
    }
}
