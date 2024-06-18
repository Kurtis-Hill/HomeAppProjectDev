<?php

    namespace App\Services\Sensor\Trigger\SensorTriggerProcessor;

use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;

interface ReadingTriggerHandlerInterface
{
    public function handleTrigger(AllSensorReadingTypeInterface $readingType): void;
}
