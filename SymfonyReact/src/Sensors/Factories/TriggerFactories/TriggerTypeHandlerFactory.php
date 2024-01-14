<?php

namespace App\Sensors\Factories\TriggerFactories;

use App\Common\Entity\TriggerType;
use App\Sensors\Exceptions\TriggerTypeNotRecognisedException;
use App\Sensors\SensorServices\Trigger\TriggerActivationHandlers\TriggerEmailProcessor;
use App\Sensors\SensorServices\Trigger\TriggerActivationHandlers\TriggerProcessorInterface;
use App\Sensors\SensorServices\Trigger\TriggerActivationHandlers\TriggerRelayActivationProcessor;

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
