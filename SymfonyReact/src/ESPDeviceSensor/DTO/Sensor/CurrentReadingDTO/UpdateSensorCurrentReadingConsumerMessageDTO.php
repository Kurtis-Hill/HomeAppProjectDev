<?php

namespace App\ESPDeviceSensor\DTO\Sensor\CurrentReadingDTO;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateSensorCurrentReadingConsumerMessageDTO
{
    private string $sensorType;

    private string $sensorName;

    #[ArrayShape(
        [
            "temperatureReading" => "float",
            "humidityReading" => "float",
            "analogReading" => "int",
        ]
    )]
    private array $currentReadings;

    private int $deviceId;

    public function __construct(
        string $sensorType,
        string $sensorName,
        array $currentReadings,
        int $deviceId,
    )
    {
        $this->sensorType = $sensorType;
        $this->currentReadings = $currentReadings;
        $this->deviceId = $deviceId;
        $this->sensorName = $sensorName;
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    #[ArrayShape(
        [
            "temperatureReading" => "float",
            "humidityReading" => "float",
            "analogReading" => "int",
        ]
    )]
    public function getCurrentReadings(): array
    {
        return $this->currentReadings;
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
