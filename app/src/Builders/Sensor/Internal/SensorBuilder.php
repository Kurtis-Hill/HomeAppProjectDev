<?php

namespace App\Builders\Sensor\Internal;

use App\Entity\Device\Devices;
use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use App\Exceptions\Sensor\DeviceNotFoundException;
use App\Exceptions\Sensor\SensorTypeNotFoundException;
use App\Repository\Device\ORM\DeviceRepository;
use App\Repository\Sensor\Sensors\ORM\SensorTypeRepository;
use App\Repository\User\ORM\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

readonly class SensorBuilder
{
    public function __construct(
        private SensorTypeRepository $sensorTypeRepository,
        private DeviceRepository $deviceRepository,
        private UserRepository $userRepository,
    ) {
    }

    public static function buildNewSensorStatic(
        string $sensorName,
        AbstractSensorType $sensorType,
        Devices $device,
        User $createdBy,
        int $pinNumber,
        int $readingInterval = Sensor::DEFAULT_READING_INTERVAL,
    ): Sensor {
        $newSensor = new Sensor();

        $newSensor->setSensorName($sensorName);
        $newSensor->setSensorTypeID(
            $sensorType
        );
        $newSensor->setDevice(
            $device
        );
        $newSensor->setCreatedBy(
            $createdBy
        );
        $newSensor->setPinNumber($pinNumber);
        $newSensor->setReadingInterval($readingInterval);

        return $newSensor;
    }

    /**
     * @throws SensorTypeNotFoundException
     * @throws UserNotFoundException
     * @throws DeviceNotFoundException
     */
    public function buildNewSensor(
        string $sensorName,
        int $sensorTypeID,
        int $deviceID,
        int $createdByID,
        int $pinNumber,
        int $readingInterval = Sensor::DEFAULT_READING_INTERVAL,
    ): Sensor {
        $sensorType = $this->sensorTypeRepository->find($sensorTypeID);
        if ($sensorType === null) {
            throw new SensorTypeNotFoundException();
        }
        $device = $this->deviceRepository->find($deviceID);
        if ($device === null) {
            throw new DeviceNotFoundException();
        }

        $createdBy = $this->userRepository->find($createdByID);
        if ($createdBy === null) {
            throw new UserNotFoundException();
        }
        return self::buildNewSensorStatic(
            sensorName: $sensorName,
            sensorType: $sensorType,
            device: $device,
            createdBy: $createdBy,
            pinNumber: $pinNumber,
            readingInterval: $readingInterval,
        );
    }
}
