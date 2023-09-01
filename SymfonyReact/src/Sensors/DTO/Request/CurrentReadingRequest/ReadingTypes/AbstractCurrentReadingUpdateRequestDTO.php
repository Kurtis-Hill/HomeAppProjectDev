<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
abstract class AbstractCurrentReadingUpdateRequestDTO
{
    protected mixed $readingTypeCurrentReading;

    public function __construct(mixed $readingTypeCurrentReading)
    {
        $this->readingTypeCurrentReading = $readingTypeCurrentReading;
    }

    public function getCurrentReading(): float|int|string
    {
        return $this->readingTypeCurrentReading;
    }

    abstract public function getReadingType(): string;
}
