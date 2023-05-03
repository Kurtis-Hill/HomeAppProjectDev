<?php

namespace App\Sensors\DTO\Response\SensorResponse;

use App\Common\Services\RequestTypeEnum;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class SensorResponseDTO
{
    public function __construct(
        private int $sensorNameID,
        private string $sensorName,
        private string $sensorType,
        private string $deviceName,
        private string $createdBy
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensorNameID(): int
    {
        return $this->sensorNameID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }
}
