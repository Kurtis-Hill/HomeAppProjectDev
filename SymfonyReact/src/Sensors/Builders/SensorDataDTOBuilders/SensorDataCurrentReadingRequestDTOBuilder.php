<?php

namespace App\Sensors\Builders\SensorDataDTOBuilders;

use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use App\Sensors\Exceptions\SensorDataCurrentReadingUpdateBuilderException;

class SensorDataCurrentReadingRequestDTOBuilder
{
    /**
     * @throws SensorDataCurrentReadingUpdateBuilderException
     */
    public static function buildSensorDataCurrentReadingUpdateDTO(mixed $sensorUpdateData): SensorDataCurrentReadingUpdateDTO
    {
        if (!is_array($sensorUpdateData)) {
            throw new SensorDataCurrentReadingUpdateBuilderException(
                SensorDataCurrentReadingUpdateBuilderException::NOT_ARRAY_ERROR_MESSAGE
            );
        }

        return new SensorDataCurrentReadingUpdateDTO(
            $sensorUpdateData['sensorName'] ?? null,
            $sensorUpdateData['sensorType'] ?? null,
            $sensorUpdateData['currentReadings'] ?? null,
        );
    }
}
