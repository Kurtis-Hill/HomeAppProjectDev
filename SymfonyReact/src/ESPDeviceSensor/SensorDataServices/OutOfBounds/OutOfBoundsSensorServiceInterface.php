<?php

namespace App\ESPDeviceSensor\SensorDataServices\OutOfBounds;

use App\Entity\Sensors\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;

interface OutOfBoundsSensorServiceInterface
{
    public function checkAndHandleSensorReadingOutOfBounds(AllSensorReadingTypeInterface $readingType): void;
}
