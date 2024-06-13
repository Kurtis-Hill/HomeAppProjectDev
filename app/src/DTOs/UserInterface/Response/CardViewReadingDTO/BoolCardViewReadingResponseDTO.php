<?php

namespace App\DTOs\UserInterface\Response\CardViewReadingDTO;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class BoolCardViewReadingResponseDTO implements CardViewReadingResponseDTOInterface
{
    public function __construct(
        private string $readingType,
        private string $updatedAt,
        private ?bool $currentReading = null,
        private ?bool $expectedReading = null,
        private ?bool $requestedReading = null,
        private ?string $readingSymbol = null,
    ) {}

    public function getReadingType(): string
    {
        return $this->readingType;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getCurrentReading(): ?bool
    {
        return $this->currentReading;
    }

    public function getExpectedReading(): ?bool
    {
        return $this->expectedReading;
    }

    public function getRequestedReading(): ?bool
    {
        return $this->requestedReading;
    }

    public function getReadingSymbol(): ?string
    {
        return $this->readingSymbol;
    }
}
