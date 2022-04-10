<?php

namespace App\Sensors\DTO\Request;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\Constraints as Assert;

#[Immutable]
class SensorDataCurrentReadingUpdateDTO
{
    #[
        Assert\Type(type: ["string"]),
        Assert\NotNull(
            message: "sensorName cannot be empty"
        ),
    ]
    private mixed $sensorName;

    #[
        Assert\Type(type: ["string"], message: "sensorType must be a {{ type }} you have provided {{ value }}"),
        Assert\NotNull(
            message: "sensorType cannot be empty"
        ),
    ]
    private mixed $sensorType;

    #[
        Assert\Type(type: ["array"], message: "currentReading must be a {{ type }} you have provided {{ value }}"),
        Assert\NotNull(
            message: "currentReadings cannot be empty"
        ),
    ]
    private mixed $currentReadings;

    public function __construct(mixed $sensorName, mixed $sensorType, mixed $currentReadings)
    {
        $this->sensorName = $sensorName;
        $this->sensorType = $sensorType;
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

    public function getCurrentReadings(): mixed
    {
        return $this->currentReadings;
    }
}
