<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;

interface NewSensorReadingTypeBuilderInterface
{
    /**
     * @throws SensorTypeBuilderFailureException
     * @throws SensorTypeException
     */
    public function buildNewReadingTypeObjects(Sensor $sensor): SensorTypeInterface;
}
