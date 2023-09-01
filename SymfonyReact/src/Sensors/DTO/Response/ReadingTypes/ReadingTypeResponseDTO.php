<?php

namespace App\Sensors\DTO\Response\ReadingTypes;

use App\Common\Services\RequestTypeEnum;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class ReadingTypeResponseDTO
{
    public function __construct(
        private int $readingTypeID,
        private string $readingType
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getReadingTypeID(): int
    {
        return $this->readingTypeID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getReadingTypeName(): string
    {
        return $this->readingType;
    }
}
