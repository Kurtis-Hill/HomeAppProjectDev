<?php

namespace App\Sensors\SensorDataServices\SensorTypeReadingTypeChecker;

interface SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool;
}
