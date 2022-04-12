<?php

namespace App\Sensors\DTO\Sensor\CurrentReadingDTO;

use App\Sensors\DTO\Request\CurrentReadingRequest\AbstractCurrentReadingUpdateRequestDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateSensorCurrentReadingConsumerMessageDTO
{
    private string $sensorType;

    private string $sensorName;

    #[ArrayShape([AbstractCurrentReadingUpdateRequestDTO::class])]
    private array $currentReadings;

    private int $deviceID;

    public function __construct(
        string $sensorType,
        string $sensorName,
        array $currentReadings,
        int $deviceID,
    ) {
        $this->sensorName = $sensorName;
        $this->sensorType = $sensorType;
        $this->currentReadings = $currentReadings;
        $this->deviceID = $deviceID;
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    #[ArrayShape([AbstractCurrentReadingUpdateRequestDTO::class])]
    public function getCurrentReadings(): array
    {
        return $this->currentReadings;
    }

    public function getDeviceID(): int
    {
        return $this->deviceID;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }
}
