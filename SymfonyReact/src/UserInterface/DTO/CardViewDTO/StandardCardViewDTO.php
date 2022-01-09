<?php

namespace App\UserInterface\DTO\CardViewDTO;

class StandardCardViewDTO
{
    private string $readingType;

    private int|float $currentReading;

    private int|float $highReading;

    private int|float $lowReading;

    private string $updatedAt;

    private ?string $readingSymbol;

    public function __construct(
        string $sensorType,
        int|float $currentReading,
        int|float $highReading,
        int|float $lowReading,
        string $updateAt,
        string $readingSymbol = null,
    ){
        $this->readingType = $sensorType;
        $this->currentReading = $currentReading;
        $this->highReading = $highReading;
        $this->lowReading = $lowReading;
        $this->updatedAt = $updateAt;
        $this->readingSymbol = $readingSymbol;
    }

    public function getReadingType(): string
    {
        return $this->readingType;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getCurrentReading(): float|int
    {
        return $this->currentReading;
    }

    public function getHighReading(): float|int
    {
        return $this->highReading;
    }

    public function getLowReading(): float|int
    {
        return $this->lowReading;
    }

    public function getReadingSymbol(): ?string
    {
        return $this->readingSymbol;
    }
}
