<?php

namespace App\Sensors\Entity\ReadingTypes;

interface BaseReadingTypeInterface
{
    public function getBaseReadingType(): BaseSensorReadingType;

    public function setBaseReadingType(BaseSensorReadingType $readingType): void;
}
