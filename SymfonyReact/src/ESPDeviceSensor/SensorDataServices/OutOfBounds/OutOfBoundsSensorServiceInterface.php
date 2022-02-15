<?php

namespace App\ESPDeviceSensor\SensorDataServices\OutOfBounds;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;

interface OutOfBoundsSensorServiceInterface
{
    public function checkAndHandleSensorReadingOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void;
}
