<?php

namespace App\Services\ESPDeviceSensor\SensorData\NewSensor;

use App\Entity\Sensors\Sensors;

interface NewSensorCreationServiceInterface
{
    public function createNewSensor(array $sensorData): ?Sensors;

    public function getServerErrors(): array;

    public function getUserInputErrors(): array;
}
