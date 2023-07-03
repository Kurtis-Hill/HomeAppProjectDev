<?php

namespace App\UserInterface\DTO\Response\CardForms\Boundary;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class BoolSensorTypeBoundaryViewFormDTO
{
    public function __construct(
        private string $readingType,
//        private bool $currentReading,
        private bool $expectedReading,
//        private bool $requestedReading,
        private bool $constRecord,
        private ?string $symbol = null,
    ) {}

    public function getReadingType(): string
    {
        return $this->readingType;
    }

//    public function getCurrentReading(): bool
//    {
//        return $this->currentReading;
//    }

    public function getExpectedReading(): bool
    {
        return $this->expectedReading;
    }

//    public function getRequestedReading(): bool
//    {
//        return $this->requestedReading;
//    }

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }
}
