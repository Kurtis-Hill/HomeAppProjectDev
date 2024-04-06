<?php

namespace App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs;

use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class UpdateSensorCurrentReadingTransportMessageDTO
{
    public function __construct(
        private string $sensorType,
        private string $sensorName,
        private array $currentReadings,
        private int $deviceID,
    ) {
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    #[ArrayShape([AbstractCurrentReadingUpdateRequestDTO::class])]
    /**
     * @return AbstractCurrentReadingUpdateRequestDTO[]
     */
    public function getCurrentReadings(): array
    {
        return $this->currentReadings;
    }

    public function getDeviceID(): int
    {
        return $this->deviceID;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }
}
