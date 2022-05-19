<?php

namespace App\Sensors\Factories\ORMFactories\SensorType;

use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Repository\ORM\SensorType\GenericSensorTypeRepositoryInterface;

interface SensorTypeRepositroyFactoryInterface
{
    /**
     * @throws SensorTypeException
     */
    public function getSensorTypeRepository(string $sensorType): GenericSensorTypeRepositoryInterface;
}
