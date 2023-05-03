<?php

namespace App\Sensors\DTO\Response\SensorResponse;

use App\Common\Services\RequestTypeEnum;
use App\Devices\DTO\Response\DeviceResponseDTO;
use App\User\DTO\Response\UserDTOs\UserFullResponseDTO;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class SensorDetailedResponseDTO
{
    public function __construct(
        private int $sensorID,
        private UserFullResponseDTO $createdBy,
        private string $sensorName,
        private DeviceResponseDTO $device,
        private SensorTypeResponseDTO $sensorType
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getSensorID(): int
    {
        return $this->sensorID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
    ])]
    public function getCreatedBy(): UserFullResponseDTO
    {
        return $this->createdBy;
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
        RequestTypeEnum::SENSITIVE_FULL->value,
    ])]
    public function getDevice(): DeviceResponseDTO
    {
        return $this->device;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
    ])]
    public function getSensorType(): SensorTypeResponseDTO
    {
        return $this->sensorType;
    }
}
