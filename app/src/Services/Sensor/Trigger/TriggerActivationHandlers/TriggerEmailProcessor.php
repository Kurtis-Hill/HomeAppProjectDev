<?php

namespace App\Services\Sensor\Trigger\TriggerActivationHandlers;

use App\Entity\Sensor\SensorTrigger;
use Symfony\Polyfill\Intl\Icu\Exception\NotImplementedException;

class TriggerEmailProcessor implements TriggerProcessorInterface
{
    public function processTrigger(SensorTrigger $sensorTrigger): void
    {
        throw new NotImplementedException('TriggerEmailHandler not implemented');
    }
}
