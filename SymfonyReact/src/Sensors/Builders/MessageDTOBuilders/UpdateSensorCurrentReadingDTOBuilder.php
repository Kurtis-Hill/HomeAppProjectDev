<?php

namespace App\Sensors\Builders\MessageDTOBuilders;

use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;

class UpdateSensorCurrentReadingDTOBuilder
{
    public static function buildUpdateSensorCurrentReadingConsumerMessageDTO(
        string $sensorType,
        string $sensorName,
        array $readingTypeCurrentReadingDTOs,
        int $deviceID,
    ): UpdateSensorCurrentReadingMessageDTO {
        return new UpdateSensorCurrentReadingMessageDTO(
            $sensorType,
            $sensorName,
            $readingTypeCurrentReadingDTOs,
            $deviceID
        );
    }
}
