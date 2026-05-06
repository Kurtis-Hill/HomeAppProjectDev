<?php

namespace App\Services\Sensor\Trigger\TriggerActivationHandlers;

use App\Entity\Sensor\SensorTrigger;
use App\Exceptions\Sensor\BaseReadingTypeNotFoundException;

interface TriggerProcessorInterface
{
    /**
     * @throws BaseReadingTypeNotFoundException
     */
    public function processTrigger(SensorTrigger $sensorTrigger): void;
}
