<?php

namespace App\Sensors\Builders\Response\TriggerResponseBuilder;

use App\Sensors\DTO\Response\Trigger\TriggerTypeResponse\TriggerFormEncapsulationDTO;

class TriggerFormEncapsulationDTOBuilder
{
    public static function buildTriggerFormEncapsulationDTO(
        array $operatorDTOs,
        array $triggerTypeDTOs,
        array $relayDTOs,
        array $sensorDTOs,
    ): TriggerFormEncapsulationDTO {
        return new TriggerFormEncapsulationDTO(
            operators: $operatorDTOs,
            triggerTypes: $triggerTypeDTOs,
            relays: $relayDTOs,
            sensors: $sensorDTOs,
        );
    }
}
