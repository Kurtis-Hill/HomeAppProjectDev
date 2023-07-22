<?php

namespace App\UserInterface\DTO\Response\CardForms\Boundary;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class BoolSensorTypeBoundaryViewFormDTO
{
    public function __construct(
        private string $readingType,
        private bool $constRecord,
        private ?bool $expectedReading = null,
        private ?string $symbol = null,
    ) {}

    public function getReadingType(): string
    {
        return $this->readingType;
    }

    public function getExpectedReading(): bool
    {
        return $this->expectedReading;
    }

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }
}
