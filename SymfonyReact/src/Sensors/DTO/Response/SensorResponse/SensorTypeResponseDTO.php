<?php

namespace App\Sensors\DTO\Response\SensorResponse;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class SensorTypeResponseDTO
{
    public function __construct(
        private int $sensorTypeID,
        private string $sensorTypeName,
        private string $sensorTypeDescription
    ) {
    }

    public function getSensorTypeID(): int
    {
        return $this->sensorTypeID;
    }

    public function getSensorTypeName(): string
    {
        return $this->sensorTypeName;
    }

    public function getSensorTypeDescription(): string
    {
        return $this->sensorTypeDescription;
    }
}
