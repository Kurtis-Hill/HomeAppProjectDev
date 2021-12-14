<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\Validator;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Exceptions\DuplicateSensorException;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use Doctrine\ORM\ORMException;

interface NewSensorCreationValidatorServiceInterface
{
    /**
     * @throws SensorTypeException
     * @throws DuplicateSensorException
     * @throws ORMException
     */
    public function createNewSensor(NewSensorDTO $newSensorDTO, Devices $device): ?Sensors;
}
