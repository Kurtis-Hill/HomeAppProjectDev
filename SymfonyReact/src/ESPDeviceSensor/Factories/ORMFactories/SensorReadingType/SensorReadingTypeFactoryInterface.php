<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType;

use App\ESPDeviceSensor\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\ESPDeviceSensor\Repository\ORM\ReadingType\ReadingTypeRepositoryInterface;

interface SensorReadingTypeFactoryInterface
{
    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    public function getSensorReadingTypeRepository(string $sensorType): ReadingTypeRepositoryInterface;
}
