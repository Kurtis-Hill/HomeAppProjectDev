<?php

namespace App\Sensors\SensorDataServices\SensorTypeReadingTypeChecker;

use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Soil;

class SoilReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->standardCheckReadingTypeIsValid($readingType, Soil::getAllowedReadingTypes());
    }
}
