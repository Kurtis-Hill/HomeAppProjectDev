<?php

namespace App\ESPDeviceSensor\DTO\Sensor;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateSensorBoundaryReadingsDTO
{
    private int $sensorReadingID;

    private string $readingType;

    private int|float|null $highReading;

    private int|float|null $lowReading;

    private int|float|null $currentHighReading;

    private int|float|null $currentLowReading;

    private ?bool $constRecord;

    public function __construct(
//        int $sensorReadingID,
        string $readingType,
        int|float|null $highReading,
        int|float|null $lowReading,
        ?bool $constRecord,
        int|float|null $currentHighReading = null,
        int|float|null $currentLowReading = null,
    ) {
//        $this->sensorReadingID = $sensorReadingID;
        $this->readingType = $readingType;
        $this->highReading = $highReading;
        $this->lowReading = $lowReading;
        $this->constRecord = $constRecord;
        $this->currentHighReading = $currentHighReading;
        $this->currentLowReading = $currentLowReading;
    }

    public function getSensorReadingID(): int
    {
        return $this->sensorReadingID;
    }

    public function getReadingType(): string
    {
        return $this->readingType;
    }

    public function getHighReading(): float|int|null
    {
        return $this->highReading;
    }

    public function getLowReading(): float|int|null
    {
        return $this->lowReading;
    }

    public function getConstRecord(): ?bool
    {
        return $this->constRecord;
    }

    public function getCurrentHighReading(): float|int|null
    {
        return $this->currentHighReading;
    }

    public function getCurrentLowReading(): float|int|null
    {
        return $this->currentLowReading;
    }
}
