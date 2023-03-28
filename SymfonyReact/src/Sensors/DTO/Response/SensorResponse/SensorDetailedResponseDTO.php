<?php

namespace App\Sensors\DTO\Response\SensorResponse;

use App\Devices\DTO\Response\DeviceResponseDTO;
use App\User\DTO\ResponseDTOs\UserDTOs\UserFullResponseDTO;
use JetBrains\PhpStorm\Immutable;

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

    public function getSensorID(): int
    {
        return $this->sensorID;
    }

    public function getCreatedBy(): UserFullResponseDTO
    {
        return $this->createdBy;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    public function getDevice(): DeviceResponseDTO
    {
        return $this->device;
    }

    public function getSensorType(): SensorTypeResponseDTO
    {
        return $this->sensorType;
    }
}
