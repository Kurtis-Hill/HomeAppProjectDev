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

    public function getCurrentReading(): mixed
    {
        return $this->readingTypeCurrentReading;
    }

    abstract public function getReadingType(): mixed;
}
