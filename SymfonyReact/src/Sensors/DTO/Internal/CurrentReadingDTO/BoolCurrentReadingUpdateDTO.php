<?php

namespace App\Sensors\DTO\Internal\CurrentReadingDTO;

readonly class BoolCurrentReadingUpdateDTO
{
    public function __construct(
        private string $readingType,
        private bool $currentReading,
    ) {}

    public function getReadingType(): string
    {
        return $this->readingType;
    }

    public function getCurrentReading(): bool
    {
        return $this->currentReading;
    }
}
