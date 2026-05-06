<?php

namespace App\Builders\Sensor\Response\TriggerResponseBuilder;

use App\DTOs\Sensor\Response\Trigger\TriggerTypeResponse\TriggerTypeResponseDTO;
use App\Entity\Sensor\TriggerType;

class TriggerTypeResponseBuilder
{
    public static function buildTriggerTypeResponseDTO(
        TriggerType $triggerType,
    ): TriggerTypeResponseDTO {
        $triggerTypeID = $triggerType->getTriggerTypeID();
        $triggerTypeName = $triggerType->getTriggerTypeName();
        $triggerTypeDescription = $triggerType->getTriggerTypeDescription();

        return new TriggerTypeResponseDTO(
            triggerTypeID: $triggerTypeID,
            triggerTypeName: $triggerTypeName,
            triggerTypeDescription: $triggerTypeDescription,
        );
    }
}
