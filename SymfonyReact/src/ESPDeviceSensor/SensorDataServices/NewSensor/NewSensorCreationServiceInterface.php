<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor;

use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Exceptions\DuplicateSensorException;
use Doctrine\ORM\ORMException;

interface NewSensorCreationServiceInterface
{
    /**
     * @throws DuplicateSensorException
     * * @throws ORMException
     */
    public function createNewSensor(NewSensorDTO $newSensorDTO): Sensors;

    public function getUserInputErrors(): array;
}
