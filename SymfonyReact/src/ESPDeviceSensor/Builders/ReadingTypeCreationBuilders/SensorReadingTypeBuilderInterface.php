<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders;

use App\ESPDeviceSensor\Entity\Sensor;

interface SensorReadingTypeBuilderInterface
{
    public function buildReadingTypeObjects(Sensor $sensor);
}
