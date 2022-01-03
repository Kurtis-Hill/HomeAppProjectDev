<?php

namespace App\ESPDeviceSensor\DTO\Sensor;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateSensorBoundaryReadingsDTO
{
    private int $sensorReadingID;

    private string $sensorType;

    private int|float $highReading;

    private int|float $lowReading;

    private bool $constRecord;

    public function __construct(
        int $sensorReadingID,
        string $sensorType,
        int|float $highReading,
        int|float $lowReading,
        bool $constRecord
    ) {
        $this->sensorReadingID = $sensorReadingID;
        $this->sensorType = $sensorType;
        $this->highReading = $highReading;
        $this->lowReading = $lowReading;
        $this->constRecord = $constRecord;
    }

    /**
     * @return int
     */
    public function getSensorReadingID(): int
    {
        return $this->sensorReadingID;
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
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
