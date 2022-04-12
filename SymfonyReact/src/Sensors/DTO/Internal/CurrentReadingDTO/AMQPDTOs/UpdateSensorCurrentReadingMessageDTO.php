<?php

namespace App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs;

use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AbstractCurrentReadingUpdateRequestDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class UpdateSensorCurrentReadingMessageDTO
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
