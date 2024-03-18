<?php

namespace App\Sensors\SensorServices\Trigger\TriggerActivationHandlers;

use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Exceptions\BaseReadingTypeNotFoundException;

interface TriggerProcessorInterface
{
    /**
     * @throws BaseReadingTypeNotFoundException
     */
    public function processTrigger(SensorTrigger $sensorTrigger): void;
}
