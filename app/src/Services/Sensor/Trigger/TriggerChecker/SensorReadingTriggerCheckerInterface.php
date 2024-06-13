<?php

namespace App\Services\Sensor\Trigger\TriggerChecker;

use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

interface SensorReadingTriggerCheckerInterface
{
    public function checkSensorForTriggers(AllSensorReadingTypeInterface $readingType, ?string $day = null, ?string $time = null): array;
}
