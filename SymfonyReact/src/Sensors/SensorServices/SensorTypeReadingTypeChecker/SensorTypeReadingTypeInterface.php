<?php

namespace App\Sensors\SensorServices\SensorTypeReadingTypeChecker;

interface SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool;
}
