<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest;

use App\Common\Services\RequestQueryParameterHandler;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandlerInterface;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\Constraints as Assert;

#[Immutable]
class SensorDataCurrentReadingUpdateDTO
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
    private mixed $sensorName;

    #[
        Assert\Type(
            type: ["string"],
            message: "sensorType must be a {{ type }} you have provided {{ value }}"
        ),
        Assert\NotNull(
            message: "sensorType cannot be empty"
        ),
        Assert\Choice(
            choices: SensorType::ALL_SENSOR_TYPES,
            message: 'sensorType must be one of {{ choices }}',
            groups: [CurrentReadingSensorDataRequestHandlerInterface::UPDATE_CURRENT_READING]
        ),
    ]
    private mixed $sensorType;

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

    public function getCurrentReadings(): ?array
    {
        return $this->currentReadings;
    }
}
