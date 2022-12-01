<?php

namespace App\Sensors\DTO\Response\SensorResponse;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class SensorTypeResponseDTO
{
    private int $sensorTypeID;

    private string $sensorTypeName;

    private string $sensorTypeDescription;

    public function __construct(
        int $sensorTypeID,
        string $sensorTypeName,
        string $sensorTypeDescription
    ) {
        $this->sensorTypeID = $sensorTypeID;
        $this->sensorTypeName = $sensorTypeName;
        $this->sensorTypeDescription = $sensorTypeDescription;
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
