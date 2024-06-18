<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

interface ReadingTypeObjectBuilderInterface
{
    /**
     * @throws \App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException
     * @throws \App\Exceptions\Sensor\SensorTypeException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     */
    public function buildReadingTypeObject(Sensor $sensor): AllSensorReadingTypeInterface;
}
