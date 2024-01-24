<?php

namespace App\Sensors\DTO\Response\TriggerTypeResponse;

readonly class TriggerTypeResponseDTO
{
    public function __construct(
        private int $triggerTypeID,
        private string $triggerTypeName,
        private string $triggerTypeDescription,
    ) {
    }

    public function getTriggerTypeID(): int
    {
        return $this->triggerTypeID;
    }

    public function getTriggerTypeName(): string
    {
        return $this->triggerTypeName;
    }

    public function getTriggerTypeDescription(): string
    {
        return $this->triggerTypeDescription;
    }
}
