<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

use App\Common\Services\RequestTypeEnum;
use App\Sensors\DTO\Response\SensorResponse\SensorDetailedResponseDTO;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class HumidityResponseDTO extends AbstractStandardResponseDTO implements StandardReadingTypeResponseInterface, SensorReadingTypeResponseDTOInterface
{
    public function __construct(
        private int $humidityID,
        SensorDetailedResponseDTO $sensorID,
        float $currentReading,
        float $highReading,
        float $lowReading,
        bool $constRecorded,
        string $updatedAt
    ) {
        parent::__construct(
            sensor: $sensorID,
            currentReading: $currentReading,
            highReading: $highReading,
            lowReading: $lowReading,
            constRecorded: $constRecorded,
            updated: $updatedAt
        );
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getHumidityID(): int
    {
        return $this->humidityID;
    }
}
