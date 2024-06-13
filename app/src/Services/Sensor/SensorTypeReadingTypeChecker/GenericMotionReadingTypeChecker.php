<?php

namespace App\Services\Sensor\SensorTypeReadingTypeChecker;

use App\Entity\Sensor\SensorTypes\GenericMotion;

class GenericMotionReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker implements SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->checkAllReadingTypeIsValid($readingType, GenericMotion::getAllowedReadingTypes());
    }
}
