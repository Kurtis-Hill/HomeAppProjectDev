<?php

namespace App\Sensors\SensorServices\TriggerChecker;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

interface SensorReadingTriggerCheckerInterface
{
    public function checkSensorForTriggers(AllSensorReadingTypeInterface $readingType);
}