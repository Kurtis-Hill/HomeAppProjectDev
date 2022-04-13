<?php

namespace App\Sensors\Factories\ORMFactories\SensorReadingType;

use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Repository\ORM\ReadingType\ReadingTypeRepositoryInterface;

interface SensorReadingTypeRepositoryFactoryInterface
{
    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    public function getSensorReadingTypeRepository(string $sensorType): ReadingTypeRepositoryInterface;
}
