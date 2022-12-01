<?php

namespace App\Sensors\DTO\Response\ReadingTypes;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class ReadingTypeResponseDTO
{
    private int $readingTypeID;

    private string $readingTypeName;

    public function __construct(int $id, string $readingType)
    {
        $this->readingTypeID = $id;
        $this->readingTypeName = $readingType;
    }

    public function getReadingTypeID(): int
    {
        return $this->readingTypeID;
    }

    public function getReadingTypeName(): string
    {
        return $this->readingTypeName;
    }
}
