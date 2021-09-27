<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor;

use App\Entity\Sensors\Sensors;

interface NewSensorCreationServiceInterface
{
    public function createNewSensor(array $sensorData): ?Sensors;

    public function getServerErrors(): array;

    public function getUserInputErrors(): array;
}
