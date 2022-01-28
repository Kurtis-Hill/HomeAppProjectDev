<?php

namespace App\ESPDeviceSensor\DTO\Sensor;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateSensorBoundaryReadingsDTO
{
    private int $sensorReadingID;

    private string $readingType;

    private int|float $highReading;

    private int|float $lowReading;

    private bool $constRecord;

    public function __construct(
        string $readingType,
        int|float $highReading,
        int|float $lowReading,
        bool $constRecord
    ) {
        $this->readingType = $readingType;
        $this->highReading = $highReading;
        $this->lowReading = $lowReading;
        $this->constRecord = $constRecord;
    }

    public function getSensorReadingID(): int
    {
        return $this->sensorReadingID;
    }

    public function getReadingType(): string
    {
        return $this->readingType;
    }

    public function getHighReading(): float|int
    {
        return $this->highReading;
    }

    public function getLowReading(): float|int
    {
        return $this->lowReading;
    }

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }
}
