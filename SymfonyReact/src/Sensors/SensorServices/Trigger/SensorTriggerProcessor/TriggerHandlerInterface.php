<?php

    namespace App\Sensors\SensorServices\Trigger\SensorTriggerProcessor;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

interface TriggerHandlerInterface
{
    public function handleTrigger(AllSensorReadingTypeInterface $readingType): void;
}
