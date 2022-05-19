<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

interface NewSensorReadingTypeBuilderInterface
{
    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws SensorTypeException
     */
    public function buildNewReadingTypeObjects(Sensor $sensor): SensorTypeInterface;
}
