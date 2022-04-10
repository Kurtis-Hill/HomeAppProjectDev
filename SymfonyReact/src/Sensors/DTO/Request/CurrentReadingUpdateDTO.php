<?php

namespace App\Sensors\DTO\Request;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\Constraints as Assert;

#[Immutable]
class CurrentReadingUpdateDTO
{
    #[Assert\Type(type: ["string"])]
    private mixed $readingType;

    #[Assert\Type(type: ["string", "integer"])]
    private mixed $readingValue;

    public function __construct(mixed $readingType, mixed $readingValue)
    {
        $this->readingType = $readingType;
        $this->readingValue = $readingValue;
    }

    public function getReadingType(): mixed
    {
        return $this->readingType;
    }

    public function getReadingValue(): mixed
    {
        return $this->readingValue;
    }
}
