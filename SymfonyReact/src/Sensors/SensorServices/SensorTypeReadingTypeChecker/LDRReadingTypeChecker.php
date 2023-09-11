<?php

namespace App\Sensors\SensorServices\SensorTypeReadingTypeChecker;

use App\Sensors\Entity\SensorTypes\GenericRelay;

class LDRReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker implements SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->checkAllReadingTypeIsValid($readingType, GenericRelay::getAllowedReadingTypes());
    }
}
