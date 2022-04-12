<?php

namespace App\Sensors\SensorDataServices\SensorTypeReadingTypeChecker;

use App\Sensors\Entity\SensorTypes\Dallas;

class DallasReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->standardCheckReadingTypeIsValid($readingType, Dallas::getAllowedReadingTypes());
    }
}
