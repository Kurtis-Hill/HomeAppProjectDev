<?php

namespace App\Sensors\SensorServices\SensorTriggerProcessor;

use App\Common\Exceptions\OperatorConvertionException;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\SensorServices\TriggerChecker\SensorReadingTriggerChecker;

readonly class TriggerHandler implements TriggerHandlerInterface
{
    public function __construct(
        private SensorReadingTriggerChecker $sensorReadingTriggerChecker,
    ) {}

    /**
     * @throws OperatorConvertionException
     */
    public function processTriggers(AllSensorReadingTypeInterface $readingType): void
    {
        $triggeredTriggers = $this->sensorReadingTriggerChecker->checkSensorForTriggers($readingType);

        if (!empty($triggeredTriggers)) {
            //send trigger to basereadingTypeToTrigger
            foreach ($triggeredTriggers as $trigger) {
                $triggerType = $trigger->getTriggerType();
                //sensor trigger factory that will build the dto for trigger type
            }
        }

        //triggger type factory and return trigger processing service
        //use the service to process the trigger
    }
}
