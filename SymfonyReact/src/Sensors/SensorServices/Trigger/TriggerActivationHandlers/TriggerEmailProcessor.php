<?php

namespace App\Sensors\SensorServices\Trigger\TriggerActivationHandlers;

use App\Sensors\Entity\SensorTrigger;
use Symfony\Polyfill\Intl\Icu\Exception\NotImplementedException;

class TriggerEmailProcessor implements TriggerProcessorInterface
{
    public function processTrigger(SensorTrigger $sensorTrigger): void
    {
        throw new NotImplementedException('TriggerEmailHandler not implemented');
    }
}
