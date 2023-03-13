<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

use App\Sensors\DTO\Response\SensorResponse\SensorFullResponseDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class AbstractStandardResponseDTO
{
    public function __construct(
        private SensorFullResponseDTO $sensor,
        private float $currentReading,
        private float $highReading,
        private float $lowReading,
        private bool $constRecordedAt,
        private string $updatedAt,
    ) {
    }

    public function getSensor(): SensorFullResponseDTO
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

    public function getConstRecordedAt(): bool
    {
        return $this->constRecordedAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
