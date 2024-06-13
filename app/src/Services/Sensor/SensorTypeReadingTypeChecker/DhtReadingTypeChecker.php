<?php

namespace App\Services\Sensor\SensorTypeReadingTypeChecker;

use App\Entity\Sensor\SensorTypes\Dht;

class DhtReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker implements SensorTypeReadingTypeInterface
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->checkAllReadingTypeIsValid($readingType, Dht::getAllowedReadingTypes());
    }

}
