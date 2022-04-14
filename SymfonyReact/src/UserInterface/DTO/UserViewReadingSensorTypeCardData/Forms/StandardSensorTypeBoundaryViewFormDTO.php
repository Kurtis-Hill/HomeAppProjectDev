<?php

namespace App\UserInterface\DTO\UserViewReadingSensorTypeCardData\Forms;

class StandardSensorTypeBoundaryViewFormDTO
{
    private string $readingType;

    private int|float|string $highReading;

    private int|float|string $lowReading;

    private bool $constRecord;

    private ?string $sensorSymbol;

    public function __construct(
        string $readingType,
        int|float|string $highReading,
        int|float|string $lowReading,
        bool $constRecord,
        ?string $sensorSymbol,
    ) {
        $this->readingType = $readingType;
        $this->highReading = $highReading;
        $this->lowReading = $lowReading;
        $this->constRecord = $constRecord;
        $this->sensorSymbol = $sensorSymbol;
    }

    public function getReadingType(): string
    {
        return $this->readingType;
    }

    public function getHighReading(): float|int|string
    {
        return $this->highReading;
    }

    public function getLowReading(): float|int|string
    {
        return $this->lowReading;
    }

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    public function getSensorSymbol(): ?string
    {
        return $this->sensorSymbol;
    }
}
