<?php

namespace App\Services\Sensor\SensorTypeReadingTypeChecker;

use App\Entity\Sensor\SensorTypes\Bmp;

class BmpReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker implements SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->checkAllReadingTypeIsValid($readingType, Bmp::getAllowedReadingTypes());
    }
}
