<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

use App\Sensors\DTO\Response\SensorResponse\SensorDetailedResponseDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class AnalogResponseDTO extends AbstractStandardResponseDTO implements StandardReadingTypeResponseInterface, SensorReadingTypeResponseDTOInterface
{
    public function __construct(
        private int $analogID,
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

    public function getAnalogID(): int
    {
        return $this->analogID;
    }

}
