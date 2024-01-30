<?php

namespace App\Common\DTO\Response;

use App\Common\Services\RequestTypeEnum;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class OperatorResponseDTO
{
    public function __construct(
        private int $operatorID,
        private string $operatorName,
        private string $operatorSymbol,
        private string $operatorDescription,
    ) {
    }

    #[
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::ONLY->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getOperatorID(): int
    {
        return $this->operatorID;
    }

    #[
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::ONLY->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getOperatorName(): string
    {
        return $this->operatorName;
    }

    #[
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::ONLY->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getOperatorSymbol(): string
    {
        return $this->operatorSymbol;
    }

    #[
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::ONLY->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getOperatorDescription(): string
    {
        return $this->operatorDescription;
    }
}
