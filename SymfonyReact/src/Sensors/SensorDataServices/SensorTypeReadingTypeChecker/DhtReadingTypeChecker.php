<?php

namespace App\Sensors\SensorDataServices\SensorTypeReadingTypeChecker;

use App\Sensors\Entity\SensorTypes\Dht;

class DhtReadingTypeChecker extends AbstractSensorTypeReadingTypeChecker
{
    public function checkReadingTypeIsValid(string $readingType): bool
    {
        return $this->standardCheckReadingTypeIsValid($readingType, Dht::getAllowedReadingTypes());
    }

}
