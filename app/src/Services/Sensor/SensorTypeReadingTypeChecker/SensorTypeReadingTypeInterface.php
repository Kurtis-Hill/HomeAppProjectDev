<?php

namespace App\Services\Sensor\SensorTypeReadingTypeChecker;

interface SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool;
}
