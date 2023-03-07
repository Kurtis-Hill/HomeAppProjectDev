<?php

namespace App\Sensors\DTO\Response\SensorResponse;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class SensorPartialResponseDTO
{
    public function __construct(
        private int $sensorNameID,
        private string $sensorName,
        private string $sensorType,
        private string $deviceName,
        private string $createdBy
    ) {
    }

    public function getSensorNameID(): int
    {
        return $this->sensorNameID;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

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
