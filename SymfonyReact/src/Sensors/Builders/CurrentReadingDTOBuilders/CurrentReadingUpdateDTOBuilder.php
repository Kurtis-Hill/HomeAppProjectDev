<?php

namespace App\Sensors\Builders\CurrentReadingDTOBuilders;

use App\Sensors\DTO\Response\CurrentReadingResponse\CurrentReadingUpdateResponseDTO;

class CurrentReadingUpdateDTOBuilder
{
    private const SENSOR_UPDATE_SUCCESS_MESSAGE = '%s data accepted for sensor %s';

    public static function buildCurrentReadingSuccessUpdateDTO(string $readingType, string $sensorName): CurrentReadingUpdateResponseDTO
    {
        return new CurrentReadingUpdateResponseDTO(
            sprintf(self::SENSOR_UPDATE_SUCCESS_MESSAGE, $readingType, $sensorName)
        );
    }

    public static function buildCurrentReadingErrorResponseDTO(string $message): CurrentReadingUpdateResponseDTO
    {
        return new CurrentReadingUpdateResponseDTO($message);
    }
}
