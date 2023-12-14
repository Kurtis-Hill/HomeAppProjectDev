<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;

class BaseReadingTypeBuilder
{
    public function buildBaseReadingTypeObject(): BaseSensorReadingType
    {
        return new BaseSensorReadingType();
    }
}
