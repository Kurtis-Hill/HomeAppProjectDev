<?php

namespace App\Sensors\DTO\Response\ReadingTypes;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class ReadingTypeResponseDTO
{
    public function __construct(
        private int $readingTypeID,
        private string $readingType
    ) {
    }

    public function getReadingTypeID(): int
    {
        return $this->readingTypeID;
    }

    public function getReadingTypeName(): string
    {
        return $this->readingType;
    }
}
