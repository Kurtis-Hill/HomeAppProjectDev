<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

use App\Sensors\DTO\Response\SensorResponse\SensorFullResponseDTO;

readonly class HumidityResponseDTO extends AbstractStandardResponseDTO implements StandardReadingTypeResponseInterface, SensorReadingTypeResponseDTOInterface
{
    public function __construct(
        private int $humidityID,
        SensorFullResponseDTO $sensorID,
        float $currentReading,
        float $highReading,
        float $lowReading,
        bool $constRecordedAt,
        string $updatedAt
    ) {
        parent::__construct(
            sensor: $sensorID,
            currentReading: $currentReading,
            highReading: $highReading,
            lowReading: $lowReading,
            constRecordedAt: $constRecordedAt,
            updatedAt: $updatedAt
        );
    }

    public function getHumidityID(): int
    {
        return $this->humidityID;
    }
}
