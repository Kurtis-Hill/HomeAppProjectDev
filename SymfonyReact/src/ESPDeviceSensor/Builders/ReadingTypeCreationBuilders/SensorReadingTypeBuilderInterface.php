<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;

interface SensorReadingTypeBuilderInterface
{
    /**
     * @throws SensorTypeBuilderFailureException
     */
    public function buildReadingTypeObjects(Sensor $sensor): SensorTypeInterface;
}
