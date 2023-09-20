<?php

namespace App\Sensors\Builders\MessageDTOBuilders;

use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;

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

    public function buildSensorSwitchRequestConsumerMessageDTO(
        int $sensorID,
        BoolCurrentReadingUpdateDTO $readingTypeCurrentReadingDTO,
    ): RequestSensorCurrentReadingUpdateMessageDTO {
        return new RequestSensorCurrentReadingUpdateMessageDTO(
            $sensorID,
            $readingTypeCurrentReadingDTO,
        );
    }
}
