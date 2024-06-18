<?php

namespace App\Builders\Sensor\Internal\SensorFilterDTOBuilders;

use App\DTOs\Sensor\Internal\Sensor\SensorFilterDTO;

class SensorFilterDTOBuilder
{
    public static function buildCardDataPreFilterDTO(
        array $sensorTypes,
        array $readingTypes
    ): SensorFilterDTO {
        return new SensorFilterDTO(
            $sensorTypes,
            $readingTypes,
        );
    }
}
