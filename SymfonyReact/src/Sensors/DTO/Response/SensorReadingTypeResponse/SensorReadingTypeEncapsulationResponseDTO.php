<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

use JetBrains\PhpStorm\ArrayShape;

readonly class SensorReadingTypeEncapsulationResponseDTO
{
    public function __construct(
        #[ArrayShape([AllSensorReadingTypeResponseDTOInterface::class])]
        private array $sensorReadingTypeData,
        private string $sensorType,
        private ?int $interval = null,
    ) {}

    #[ArrayShape([AllSensorReadingTypeResponseDTOInterface::class])]
    public function getSensorReadingTypeData(): array
    {
        return $this->sensorReadingTypeData;
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    public function getInterval(): ?int
    {
        return $this->interval;
    }
}
