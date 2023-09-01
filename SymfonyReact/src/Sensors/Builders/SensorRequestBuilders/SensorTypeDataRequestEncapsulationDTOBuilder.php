<?php

namespace App\Sensors\Builders\SensorRequestBuilders;

use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorTypeDataRequestEncapsulationDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use JetBrains\PhpStorm\ArrayShape;

class SensorTypeDataRequestEncapsulationDTOBuilder
{
    public static function buildSensorTypeDataRequestDTO(
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        ?array $relay = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        ?array $dht = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        ?array $dallas = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        ?array $soil = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        ?array $motion = null,
        #[ArrayShape([SingleSensorUpdateRequestDTO::class])]
        ?array $bmp = null,
    ): SensorTypeDataRequestEncapsulationDTO {
        return new SensorTypeDataRequestEncapsulationDTO(
            relay: $relay,
            dht: $dht,
            dallas: $dallas,
            soil: $soil,
            motion: $motion,
            bmp: $bmp,
        );
    }
}
