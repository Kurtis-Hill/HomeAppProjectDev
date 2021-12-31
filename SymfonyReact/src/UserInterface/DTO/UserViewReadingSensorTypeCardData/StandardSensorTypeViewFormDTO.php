<?php

namespace App\UserInterface\DTO\UserViewReadingSensorTypeCardData;

class StandardSensorTypeViewFormDTO
{
    private string $sensorType;
    private int|float $highReading;
    private int|float $lowReading;
    private bool $constRecord;

    public function __construct(
        string $sensorType,
        int|float $highReading,
        int|float $lowReading,
        bool $constRecord,
    ) {
        $this->sensorType = $sensorType;
        $this->highReading = $highReading;
        $this->lowReading = $lowReading;
        $this->constRecord = $constRecord;
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
