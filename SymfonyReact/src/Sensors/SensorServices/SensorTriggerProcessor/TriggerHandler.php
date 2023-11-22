<?php

namespace App\Sensors\SensorServices\SensorTriggerProcessor;

use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\SensorServices\TriggerChecker\SensorReadingTriggerChecker;

readonly class TriggerHandler implements TriggerHandlerInterface
{
    public function __construct(
        private SensorReadingTriggerChecker $sensorReadingTriggerChecker,
    ) {}

    public function processTriggers(AllSensorReadingTypeInterface $readingType): void
    {
        $triggeredTriggers = $this->sensorReadingTriggerChecker->checkSensorForTriggers($readingType);

        //triggger type factory and return trigger processing service
        //use the service to process the trigger
    }
}
