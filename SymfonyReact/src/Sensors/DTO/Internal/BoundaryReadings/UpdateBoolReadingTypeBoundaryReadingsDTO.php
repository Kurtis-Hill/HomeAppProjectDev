<?php

namespace App\Sensors\DTO\Internal\BoundaryReadings;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class UpdateBoolReadingTypeBoundaryReadingsDTO implements UpdateBoundaryReadingDTOInterface
{
    public function __construct(
        private string $readingType,
        private bool $currentExpectedReading,
        private bool $currentConstRecord,
        private bool $newExpectedReading,
        private bool $newConstRecord,
    ) {
    }

    public function getReadingType(): string
    {
        return $this->readingType;
    }

    public function getNewExpectedReading(): bool
    {
        return $this->newExpectedReading;
    }

    public function getNewConstRecord(): bool
    {
        return $this->newConstRecord;
    }

    public function getCurrentExpectedReading(): bool
    {
        return $this->currentExpectedReading;
    }

    public function getCurrentConstRecord(): bool
    {
        return $this->currentConstRecord;
    }
}
