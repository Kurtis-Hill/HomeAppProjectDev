<?php

namespace App\ESPDeviceSensor\SensorDataServices\OutOfBounds;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;

interface OutOfBoundsSensorServiceInterface
{
    public function checkAndHandleSensorReadingOutOfBounds(AllSensorReadingTypeInterface $readingType): void;
}
