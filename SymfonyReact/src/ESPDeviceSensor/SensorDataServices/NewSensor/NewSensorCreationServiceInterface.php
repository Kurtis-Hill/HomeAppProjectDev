<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Exceptions\DuplicateSensorException;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use Doctrine\ORM\ORMException;

interface NewSensorCreationServiceInterface
{
    public function validateSensor(Sensor $sensor): array;

    public function createNewSensor(NewSensorDTO $newSensorDTO): Sensor;

    public function saveNewSensor(Sensor $sensor): bool;
}
