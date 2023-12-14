<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

interface ReadingTypeObjectBuilderInterface
{
    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws SensorTypeException
     * @throws ORMException
     * @throws ORMInvalidArgumentException
     */
    public function buildReadingTypeObject(Sensor $sensor): void;
}
