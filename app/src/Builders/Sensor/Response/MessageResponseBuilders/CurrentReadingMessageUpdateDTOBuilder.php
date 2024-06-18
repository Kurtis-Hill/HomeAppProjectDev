<?php
declare(strict_types=1);

namespace App\Builders\Sensor\Response\MessageResponseBuilders;

use App\DTOs\Sensor\Response\CurrentReadingResponse\CurrentReadingUpdateResponseDTO;

class CurrentReadingMessageUpdateDTOBuilder
{
    private const SENSOR_UPDATE_SUCCESS_MESSAGE = '%s data accepted for sensor %s';

    public static function buildCurrentReadingSuccessUpdateDTO(string $readingType, string $sensorName): CurrentReadingUpdateResponseDTO
    {
        return new CurrentReadingUpdateResponseDTO(
            sprintf(self::SENSOR_UPDATE_SUCCESS_MESSAGE, $readingType, $sensorName)
        );
    }
}
