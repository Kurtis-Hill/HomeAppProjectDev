<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor;

use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\ESPDeviceSensor\Entity\Sensors;

interface NewSensorCreationServiceInterface
{
    public function createNewSensor(NewSensorDTO $newSensorDTO): ?Sensors;

    public function getServerErrors(): array;

    public function getUserInputErrors(): array;
}
