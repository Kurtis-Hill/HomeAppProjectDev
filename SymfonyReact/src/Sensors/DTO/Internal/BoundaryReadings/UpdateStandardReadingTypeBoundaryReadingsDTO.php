<?php

namespace App\Sensors\DTO\Internal\BoundaryReadings;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateStandardReadingTypeBoundaryReadingsDTO
{
    private int $sensorReadingID;

    private string $readingType;

    private int|float|null $highReading;

    private int|float|null $lowReading;

    private bool $currentConstRecord;

    private int|float|null $currentHighReading;

    private int|float|null $currentLowReading;

    private ?bool $constRecord;

    public function __construct(
        string $readingType,
        int|float $currentHighReading,
        int|float $currentLowReading,
        bool $currentConstRecord,
        int|float|null $highReading,
        int|float|null $lowReading,
        ?bool $constRecord,
    ) {
        $this->readingType = $readingType;
        $this->highReading = $highReading;
        $this->lowReading = $lowReading;
        $this->currentConstRecord = $currentConstRecord;
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

    public function isCurrentConstRecord(): bool
    {
        return $this->currentConstRecord;
    }

    public function getCurrentHighReading(): float|int
    {
        return $this->currentHighReading;
    }

    public function getCurrentLowReading(): float|int
    {
        return $this->currentLowReading;
    }


}
