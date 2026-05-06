<?php

namespace App\Factories\Sensor\TriggerFactories;

use App\Entity\Sensor\TriggerType;
use App\Exceptions\Sensor\TriggerTypeNotRecognisedException;
use App\Services\Sensor\Trigger\TriggerActivationHandlers\TriggerEmailProcessor;
use App\Services\Sensor\Trigger\TriggerActivationHandlers\TriggerProcessorInterface;
use App\Services\Sensor\Trigger\TriggerActivationHandlers\TriggerRelayActivationProcessor;

readonly class TriggerTypeHandlerFactory
{
    public function __construct(
        private TriggerRelayActivationProcessor $triggerRelayActivationHandler,
        private TriggerEmailProcessor $triggerEmailHandler,
    ) {
    }

    /**
     * @throws TriggerTypeNotRecognisedException
     */
    public function getTriggerTypeHandler(string $triggerType): TriggerProcessorInterface
    {
        return match ($triggerType) {
            TriggerType::RELAY_UP_TRIGGER, TriggerType::RELAY_DOWN_TRIGGER => $this->triggerRelayActivationHandler,
            TriggerType::EMAIL_TRIGGER => $this->triggerEmailHandler,
            default => throw new TriggerTypeNotRecognisedException(
                sprintf(
                    TriggerTypeNotRecognisedException::MESSAGE,
                    $triggerType
                )
            ),
        };
    }
}
