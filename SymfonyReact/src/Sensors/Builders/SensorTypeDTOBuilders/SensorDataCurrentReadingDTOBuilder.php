<?php

namespace App\Sensors\Builders\SensorTypeDTOBuilders;

use App\Sensors\DTO\Request\SensorDataCurrentReadingUpdateDTO;

class SensorDataCurrentReadingDTOBuilder
{
    public static function buildSensorDataCurrentReadingUpdateDTO(array $sensorUpdateData): SensorDataCurrentReadingUpdateDTO
    {
        return new SensorDataCurrentReadingUpdateDTO(
            $sensorUpdateData['sensorName'] ?? null,
            $sensorUpdateData['sensorType'] ?? null,
            $sensorUpdateData['currentReadings'] ?? null,
        );
    }
}
