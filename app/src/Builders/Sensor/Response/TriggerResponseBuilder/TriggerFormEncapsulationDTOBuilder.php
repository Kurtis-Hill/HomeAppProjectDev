<?php

namespace App\Builders\Sensor\Response\TriggerResponseBuilder;

use App\DTOs\Sensor\Response\Trigger\TriggerTypeResponse\TriggerFormEncapsulationDTO;

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
