<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\SensorType;

use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Repository\ORM\SensorType\SensorTypeRepositoryInterface;

interface SensorTypeFactoryInterface
{
    /**
     * @throws SensorTypeException
     */
    public function getSensorTypeRepository(string $sensorType): SensorTypeRepositoryInterface;
}
