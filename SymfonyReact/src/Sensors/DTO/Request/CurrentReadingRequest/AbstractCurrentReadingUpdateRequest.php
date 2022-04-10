<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest;

abstract class AbstractCurrentReadingUpdateRequest
{
    protected float|int|string $readingTypeCurrentReading;

    public function __construct(float|int|string $readingTypeCurrentReading)
    {
        $this->readingTypeCurrentReading = $readingTypeCurrentReading;
    }

    public function getCurrentReading(): float|int|string
    {
        return $this->readingTypeCurrentReading;
    }
}
