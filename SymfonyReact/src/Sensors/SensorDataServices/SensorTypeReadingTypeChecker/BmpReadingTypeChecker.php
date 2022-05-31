<?php

namespace App\Sensors\SensorDataServices\SensorTypeReadingTypeChecker;

use App\Sensors\Entity\SensorTypes\Bmp;

class BmpReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker implements SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->standardCheckReadingTypeIsValid($readingType, Bmp::getAllowedReadingTypes());
    }
}
