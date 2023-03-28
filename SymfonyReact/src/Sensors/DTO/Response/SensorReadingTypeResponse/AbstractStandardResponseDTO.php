<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

use App\Sensors\DTO\Response\SensorResponse\SensorDetailedResponseDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class AbstractStandardResponseDTO
{
    public function __construct(
        private SensorDetailedResponseDTO $sensor,
        private float $currentReading,
        private float $highReading,
        private float $lowReading,
        private bool $constRecorded,
        private string $updated,
    ) {
    }

    public function getSensor(): SensorDetailedResponseDTO
    {
        return $this->sensor;
    }

    public function getCurrentReading(): float|int|string
    {
        return $this->currentReading;
    }

    public function getHighReading(): float|int|string
    {
        return $this->highReading;
    }

    public function getLowReading(): float|int|string
    {
        return $this->lowReading;
    }

    public function getConstRecorded(): bool
    {
        return $this->constRecorded;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated;
    }
}
