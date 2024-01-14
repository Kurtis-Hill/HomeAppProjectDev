<?php

namespace App\Sensors\SensorServices\Trigger\TriggerActivationHandlers;

use App\Sensors\Entity\SensorTrigger;

interface TriggerProcessorInterface
{
    public function processTrigger(SensorTrigger $sensorTrigger): void;
}
