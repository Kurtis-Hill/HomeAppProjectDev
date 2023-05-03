<?php

namespace App\Sensors\DTO\Response\SensorReadingTypeResponse;

use App\Common\Services\RequestTypeEnum;
use App\Sensors\DTO\Response\SensorResponse\SensorDetailedResponseDTO;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class AbstractStandardResponseDTO
{
    public function __construct(
        private SensorDetailedResponseDTO $sensor,
        private float $currentReading,
        private float $highReading,
        private float $lowReading,
        private bool $constRecorded,
        private string $updated,
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensor(): SensorDetailedResponseDTO
    {
        return $this->sensor;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getCurrentReading(): float|int|string
    {
        return $this->currentReading;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getHighReading(): float|int|string
    {
        return $this->highReading;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getLowReading(): float|int|string
    {
        return $this->lowReading;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getConstRecorded(): bool
    {
        return $this->constRecorded;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getUpdatedAt(): string
    {
        return $this->updated;
    }
}
