<?php

namespace App\Sensors\Builders\Request\SensorDataDTOBuilders;

use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateRequestDTO;

class SensorDataCurrentReadingRequestDTOBuilder
{
    public static function buildSensorDataCurrentReadingUpdateRequestDTO(
        mixed $sensorName = null,
        mixed $sensorType = null,
        mixed $currentReadings = null,
    ): SensorDataCurrentReadingUpdateRequestDTO {
        return new SensorDataCurrentReadingUpdateRequestDTO(
            $sensorName,
            $sensorType,
            $currentReadings,
        );
    }
}
