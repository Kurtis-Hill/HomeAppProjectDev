<?php

namespace App\Sensors\Builders\Response\TriggerResponseBuilder;

use App\Common\Entity\TriggerType;
use App\Sensors\DTO\Response\Trigger\TriggerTypeResponse\TriggerTypeResponseDTO;

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