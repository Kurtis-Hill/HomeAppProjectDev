<?php

namespace App\Entity\Sensor\ReadingTypes;

interface BaseReadingTypeInterface
{
    public function getBaseReadingType(): BaseSensorReadingType;

    public function setBaseReadingType(BaseSensorReadingType $readingType): void;
}
