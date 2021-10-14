<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\SensorType;

use App\ESPDeviceSensor\Repository\ORM\SensorType\SensorTypeRepositoryInterface;

interface SensorTypeFactoryInterface
{
    public function getSensorTypeRepository(string $sensorType): SensorTypeRepositoryInterface;
}
