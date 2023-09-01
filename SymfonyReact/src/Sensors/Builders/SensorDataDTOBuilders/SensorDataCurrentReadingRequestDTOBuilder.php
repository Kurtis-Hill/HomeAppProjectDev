<?php

namespace App\Sensors\Builders\SensorDataDTOBuilders;

use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use App\Sensors\Exceptions\SensorDataCurrentReadingUpdateBuilderException;

class SensorDataCurrentReadingRequestDTOBuilder
{
    /**
     * @throws SensorDataCurrentReadingUpdateBuilderException
     */
    public static function buildSensorDataCurrentReadingUpdateDTO(
        mixed $sensorName = null,
        mixed $sensorType = null,
        mixed $currentReadings = null,
    ): SensorDataCurrentReadingUpdateDTO {
        return new SensorDataCurrentReadingUpdateDTO(
            $sensorName,
            $sensorType,
            $currentReadings,
        );
    }
}
