<?php
declare(strict_types=1);

namespace App\Sensors\Builders\MessageDTOBuilders;

use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateTransportMessageDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingTransportMessageDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;

class UpdateSensorCurrentReadingTransportDTOBuilder
{
    public static function buildUpdateSensorCurrentReadingConsumerMessageDTO(
        string $sensorType,
        string $sensorName,
        array $readingTypeCurrentReadingDTOs,
        int $deviceID,
    ): UpdateSensorCurrentReadingTransportMessageDTO {
        return new UpdateSensorCurrentReadingTransportMessageDTO(
            $sensorType,
            $sensorName,
            $readingTypeCurrentReadingDTOs,
            $deviceID
        );
    }

    public function buildSensorSwitchRequestConsumerMessageDTO(
        int $sensorID,
        BoolCurrentReadingUpdateDTO $readingTypeCurrentReadingDTO,
    ): RequestSensorCurrentReadingUpdateTransportMessageDTO {
        return new RequestSensorCurrentReadingUpdateTransportMessageDTO(
            $sensorID,
            $readingTypeCurrentReadingDTO,
        );
    }
}
