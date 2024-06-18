<?php

namespace App\Services\Sensor\SensorTypeReadingTypeChecker;

use App\Entity\Sensor\SensorTypes\LDR;

class LDRReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker implements SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->checkAllReadingTypeIsValid($readingType, LDR::getAllowedReadingTypes());
    }
}
