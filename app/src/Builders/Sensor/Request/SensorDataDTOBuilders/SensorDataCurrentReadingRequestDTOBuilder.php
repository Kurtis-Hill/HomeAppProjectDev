<?php

namespace App\Builders\Sensor\Request\SensorDataDTOBuilders;

use App\DTOs\Sensor\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateRequestDTO;

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
