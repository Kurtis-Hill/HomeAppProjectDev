<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\SensorType;

use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Repository\ORM\SensorType\GenericSensorTypeRepositoryInterface;

interface SensorTypeRepositroyFactoryInterface
{
    /**
     * @throws SensorTypeException
     */
    public function getSensorTypeRepository(string $sensorType): GenericSensorTypeRepositoryInterface;
}
