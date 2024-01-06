<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\Sensor;

class BaseReadingTypeBuilder
{
    public function buildBaseReadingTypeObject(Sensor $sensor): BaseSensorReadingType
    {
        $baseSensorReadingType = new BaseSensorReadingType();

        $baseSensorReadingType->setSensor($sensor);
        $baseSensorReadingType->setCreatedAt();

        return $baseSensorReadingType;
    }
}
