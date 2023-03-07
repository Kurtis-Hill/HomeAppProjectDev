<?php

namespace App\Sensors\DTO\Internal\BoundaryReadings;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class UpdateStandardReadingTypeBoundaryReadingsDTO
{
    public function __construct(
        private string $readingType,
        private int|float $currentHighReading,
        private int|float $currentLowReading,
        private bool $currentConstRecord,
        private int|float|null $highReading,
        private int|float|null $lowReading,
        private ?bool $constRecord,
    ) {
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
