<?php

namespace App\DTOs\Sensor\Request\CurrentReadingRequest;

use App\Services\Sensor\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandlerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SwitchSensorDataCurrentReadingUpdateRequestDTO
{
    #[
        Assert\Type(
            type: ["string"],
            message: "sensorName must be a {{ type }} you have provided {{ value }}",
            groups: [CurrentReadingSensorDataRequestHandlerInterface::SEND_UPDATE_CURRENT_READING]
        ),
        Assert\NotNull(
            message: "sensorName cannot be empty",
            groups: [CurrentReadingSensorDataRequestHandlerInterface::SEND_UPDATE_CURRENT_READING]
        ),
    ]
    private mixed $sensorName;

    #[
        Assert\Type(
            type: ["array"],
            message: "currentReading must be a {{ type }}",
            groups: [CurrentReadingSensorDataRequestHandlerInterface::SEND_UPDATE_CURRENT_READING]
        ),
        Assert\NotNull(
            message: "currentReadings cannot be empty",
            groups: [CurrentReadingSensorDataRequestHandlerInterface::SEND_UPDATE_CURRENT_READING]
        ),
    ]
    private mixed $currentReadings;

    public function __construct(mixed $sensorName, mixed $currentReadings)
    {
        $this->sensorName = $sensorName;
        $this->currentReadings = $currentReadings;
    }

    public function setSensorName(mixed $sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    public function setCurrentReadings(mixed $currentReadings): void
    {
        $this->currentReadings = $currentReadings;
    }

    public function getSensorName(): mixed
    {
        return $this->sensorName;
    }

    public function getCurrentReadings(): mixed
    {
        return $this->currentReadings;
    }
}
