<?php

namespace App\Sensors\SensorServices\SensorTriggerProcessor;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

interface TriggerHandlerInterface
{
    public function processTriggers(AllSensorReadingTypeInterface $readingType): void;
}
