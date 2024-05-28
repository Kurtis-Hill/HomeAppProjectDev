<?php

namespace App\Sensors\SensorServices\SensorTypeReadingTypeChecker;

use App\Sensors\Entity\SensorTypes\GenericMotion;

class GenericMotionReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker implements SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->checkAllReadingTypeIsValid($readingType, GenericMotion::getAllowedReadingTypes());
    }
}
