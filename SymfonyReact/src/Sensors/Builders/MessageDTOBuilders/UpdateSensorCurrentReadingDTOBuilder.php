<?php

namespace App\Sensors\Builders\MessageDTOBuilders;

use App\Sensors\DTO\Sensor\CurrentReadingDTO\UpdateSensorCurrentReadingConsumerMessageDTO;

class UpdateSensorCurrentReadingDTOBuilder
{
    public static function buildUpdateSensorCurrentReadingConsumerMessageDTO(
        string $sensorType,
        string $sensorName,
        array $readingTypeCurrentReadingDTOs,
        int $deviceID,
    ): UpdateSensorCurrentReadingConsumerMessageDTO {
        return new UpdateSensorCurrentReadingConsumerMessageDTO(
            $sensorType,
            $sensorName,
            $readingTypeCurrentReadingDTOs,
            $deviceID
        );
    }
}
