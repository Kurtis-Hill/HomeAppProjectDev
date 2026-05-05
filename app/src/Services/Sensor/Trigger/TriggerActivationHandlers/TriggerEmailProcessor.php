<?php

namespace App\Services\Sensor\Trigger\TriggerActivationHandlers;

use App\Entity\Sensor\SensorTrigger;
use Psr\Log\LoggerInterface;

readonly class TriggerEmailProcessor implements TriggerProcessorInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function processTrigger(SensorTrigger $sensorTrigger): void
    {
        $this->logger->warning(
            sprintf(
                'Email trigger type is not yet implemented — trigger ID %d was skipped.',
                $sensorTrigger->getSensorTriggerID()
            )
        );
    }
}
