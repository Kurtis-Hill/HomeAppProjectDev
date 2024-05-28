<?php

namespace App\Sensors\Builders\Internal\SensorCreationBuilders;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DeviceNotFoundException;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\Repository\Sensors\SensorTypeRepositoryInterface;
use App\User\Entity\User;

readonly class NewSensorDTOBuilder
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
        private SensorTypeRepositoryInterface $sensorTypeRepository,
    ) {}

    /**
     * @throws SensorTypeNotFoundException
     * @throws DeviceNotFoundException
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
