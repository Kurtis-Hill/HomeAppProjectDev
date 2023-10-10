<?php

namespace App\Sensors\SensorServices\SensorTypeReadingTypeChecker;

use App\Sensors\Entity\SensorTypes\Sht;

class ShtReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker implements SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->checkAllReadingTypeIsValid($readingType, Sht::getAllowedReadingTypes());
    }
}
