<?php

namespace App\DTOs\Sensor\Response\Trigger\TriggerTypeResponse;

use App\Services\Request\RequestTypeEnum;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class TriggerTypeResponseDTO
{
    public function __construct(
        private int $triggerTypeID,
        private string $triggerTypeName,
        private string $triggerTypeDescription,
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
    public function getTriggerTypeID(): int
    {
        return $this->triggerTypeID;
    }

    #[
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::ONLY->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getTriggerTypeName(): string
    {
        return $this->triggerTypeName;
    }

    #[
        Groups([
            RequestTypeEnum::FULL->value,
            RequestTypeEnum::ONLY->value,
            RequestTypeEnum::SENSITIVE_FULL->value,
            RequestTypeEnum::SENSITIVE_ONLY->value,
        ])
    ]
    public function getTriggerTypeDescription(): string
    {
        return $this->triggerTypeDescription;
    }
}
