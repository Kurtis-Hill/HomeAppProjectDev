<?php

namespace App\Sensors\Builders\TriggerResponseBuilder;

use App\Sensors\DTO\Response\TriggerTypeResponse\TriggerFormEncapsulationDTO;

class TriggerFormEncapsulationDTOBuilder
{
    public static function buildTriggerFormEncapsulationDTO(
        array $operatorDTOs,
        array $triggerTypeDTOs,
        array $relayDTOs,
        array $sensorDTOs,
    ): TriggerFormEncapsulationDTO {
        return new TriggerFormEncapsulationDTO(
            operatorDTOs: $operatorDTOs,
            triggerTypeDTOs: $triggerTypeDTOs,
            relayDTOs: $relayDTOs,
            sensorDTOs: $sensorDTOs,
        );
    }
}
