<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\Sensor;

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
