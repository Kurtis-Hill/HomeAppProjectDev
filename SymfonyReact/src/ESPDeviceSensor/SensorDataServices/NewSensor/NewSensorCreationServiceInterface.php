<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor;

use App\Entity\Sensors\Sensors;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;

interface NewSensorCreationServiceInterface
{
    public function createNewSensor(NewSensorDTO $newSensorDTO): ?Sensors;

    public function getServerErrors(): array;

    public function getUserInputErrors(): array;
}
