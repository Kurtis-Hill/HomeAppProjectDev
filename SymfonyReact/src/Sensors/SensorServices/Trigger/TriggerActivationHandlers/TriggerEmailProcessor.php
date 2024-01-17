<?php

namespace App\Sensors\SensorServices\Trigger\TriggerActivationHandlers;

use App\Sensors\Entity\SensorTrigger;
use Symfony\Polyfill\Intl\Icu\Exception\NotImplementedException;

class TriggerEmailHandler implements TriggerHandlerInterface
{
    public function processTrigger(SensorTrigger $sensorTrigger): void
    {
        throw new NotImplementedException('TriggerEmailHandler not implemented');
    }
}
