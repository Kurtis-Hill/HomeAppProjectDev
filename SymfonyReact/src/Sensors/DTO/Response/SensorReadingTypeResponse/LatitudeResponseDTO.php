<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

use App\Common\Services\RequestTypeEnum;
use App\Sensors\DTO\Response\SensorResponse\SensorDetailedResponseDTO;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class LatitudeResponseDTO extends AbstractStandardResponseDTO implements StandardReadingTypeResponseInterface, SensorReadingTypeResponseDTOInterface
{
    public function __construct(
        private int $latitudeID,
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
    public function getLatitudeID(): int
    {
        return $this->latitudeID;
    }
}
