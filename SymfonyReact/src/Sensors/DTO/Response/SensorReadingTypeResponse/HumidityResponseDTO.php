<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

use App\Sensors\DTO\Response\SensorResponse\SensorDetailedResponseDTO;

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

    public function getHumidityID(): int
    {
        return $this->humidityID;
    }
}
