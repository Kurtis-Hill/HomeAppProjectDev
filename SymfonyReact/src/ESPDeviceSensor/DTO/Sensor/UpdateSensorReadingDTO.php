<?php

namespace App\ESPDeviceSensor\DTO\Sensor;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateSensorReadingDTO
{
    private string $sensorType;

    private string $sensorName;

    private array $sensorData;

    private int $deviceId;

    public function __construct(
        string $sensorType,
        string $sensorName,
        array $currentReadings,
        int $deviceId,
    )
    {
        $this->sensorType = $sensorType;
        $this->sensorData = $currentReadings;
        $this->deviceId = $deviceId;
        $this->sensorName = $sensorName;
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    public function getCurrentReadings(): array
    {
        return $this->sensorData;
    }

    public function getDeviceId(): int
    {
        return $this->deviceId;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }
}
