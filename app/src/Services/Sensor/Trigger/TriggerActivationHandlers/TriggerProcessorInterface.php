<?php

namespace App\Services\Sensor\Trigger\TriggerActivationHandlers;

use App\Entity\Sensor\SensorTrigger;

interface TriggerProcessorInterface
{
    /**
     * @throws \App\Exceptions\Sensor\BaseReadingTypeNotFoundException
     */
    public function processTrigger(SensorTrigger $sensorTrigger): void;
}
