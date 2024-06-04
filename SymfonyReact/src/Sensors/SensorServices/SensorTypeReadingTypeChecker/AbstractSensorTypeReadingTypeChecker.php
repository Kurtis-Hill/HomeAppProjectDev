<?php

namespace App\Sensors\SensorServices\SensorTypeReadingTypeChecker;

use App\Sensors\Exceptions\ReadingTypeNotSupportedException;

abstract class AbstractSensorTypeReadingTypeChecker
{
    /**
     * @throws ReadingTypeNotSupportedException
     */
    protected function checkAllReadingTypeIsValid(string $readingType, array $allowedReadingTypes): bool
    {
        if (!in_array($readingType, $allowedReadingTypes, true)) {
            return false;
        }

        return true;
    }
}
