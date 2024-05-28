<?php

    namespace App\Sensors\SensorServices\Trigger\SensorTriggerProcessor;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

interface ReadingTriggerHandlerInterface
{
    public function handleTrigger(AllSensorReadingTypeInterface $readingType): void;
}
