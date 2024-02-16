<?php

namespace App\Sensors\Builders\Internal\SensorFilterDTOBuilders;

use App\Sensors\DTO\Internal\Sensor\SensorFilterDTO;

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
