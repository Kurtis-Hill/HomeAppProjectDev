<?php

namespace App\Services\Sensor\SensorTypeReadingTypeChecker;

use App\Entity\Sensor\SensorTypes\Dallas;

class DallasReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker implements SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->checkAllReadingTypeIsValid($readingType, Dallas::getAllowedReadingTypes());
    }
}
