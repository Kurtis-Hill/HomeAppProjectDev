<?php

namespace App\DTOs\Sensor\Request\CurrentReadingRequest;

use App\Entity\Sensor\AbstractSensorType;
use App\Services\Sensor\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandlerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SensorDataCurrentReadingUpdateRequestDTO
{
    #[
        Assert\Type(
            type: ["string"],
            message: "sensorName must be a {{ type }} you have provided {{ value }}",
            groups: [CurrentReadingSensorDataRequestHandlerInterface::SEND_UPDATE_CURRENT_READING, CurrentReadingSensorDataRequestHandlerInterface::UPDATE_CURRENT_READING]
        ),
        Assert\NotNull(
            message: "sensorName cannot be empty",
            groups: [CurrentReadingSensorDataRequestHandlerInterface::SEND_UPDATE_CURRENT_READING, CurrentReadingSensorDataRequestHandlerInterface::UPDATE_CURRENT_READING]
        ),
    ]
    private string $sensorName;

    #[
        Assert\Type(
            type: ["string"],
            message: "sensorType must be a {{ type }} you have provided {{ value }}"
        ),
        Assert\NotNull(
            message: "sensorType cannot be empty"
        ),
        Assert\Choice(
            choices: AbstractSensorType::ALL_SENSOR_TYPES,
            message: 'sensorType must be one of {{ choices }}',
            groups: [CurrentReadingSensorDataRequestHandlerInterface::UPDATE_CURRENT_READING]
        ),
    ]
    private string $sensorType;

    #[
        Assert\Type(
            type: ["array"],
            message: "currentReading must be a {{ type }}",
            groups: [CurrentReadingSensorDataRequestHandlerInterface::SEND_UPDATE_CURRENT_READING, CurrentReadingSensorDataRequestHandlerInterface::UPDATE_CURRENT_READING]
        ),
        Assert\NotNull(
            message: "currentReadings cannot be empty",
            groups: [CurrentReadingSensorDataRequestHandlerInterface::SEND_UPDATE_CURRENT_READING, CurrentReadingSensorDataRequestHandlerInterface::UPDATE_CURRENT_READING]
        ),
    ]
    private array $currentReadings;

    public function __construct(mixed $sensorName, mixed $sensorType, mixed $currentReadings)
    {
        $this->sensorName = $sensorName;
        $this->sensorType = $sensorType;
        $this->currentReadings = $currentReadings;
    }

    public function setSensorName(mixed $sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    public function setSensorType(mixed $sensorType): void
    {
        $this->sensorType = $sensorType;
    }

    public function setCurrentReadings(mixed $currentReadings): void
    {
        $this->currentReadings = $currentReadings;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    public function getSensorType(): mixed
    {
        return $this->sensorType;
    }

    public function getCurrentReadings(): ?array
    {
        return $this->currentReadings;
    }
}
