<?php

namespace App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType;

use App\ESPDeviceSensor\Repository\ORM\ReadingType\ReadingTypeRepositoryInterface;

interface SensorReadingTypeFactoryInterface
{
    public function getSensorReadingTypeRepository(string $sensorType): ReadingTypeRepositoryInterface;
}
