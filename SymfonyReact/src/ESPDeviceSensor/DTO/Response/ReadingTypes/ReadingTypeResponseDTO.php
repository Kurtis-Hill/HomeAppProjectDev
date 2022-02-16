<?php

namespace App\ESPDeviceSensor\DTO\Response\ReadingTypes;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class ReadingTypeResponseDTO
{
    private int $id;

    private string $readingType;

    public function __construct(int $id, string $readingType)
    {
        $this->id = $id;
        $this->readingType = $readingType;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getReadingType(): string
    {
        return $this->readingType;
    }
}
