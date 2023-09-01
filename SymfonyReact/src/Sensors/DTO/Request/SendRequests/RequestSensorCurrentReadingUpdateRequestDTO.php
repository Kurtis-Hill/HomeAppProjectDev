<?php

namespace App\Sensors\DTO\Request\SendRequests;

use App\Devices\DTO\Request\DeviceRequest\DeviceRequestDTOInterface;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class RequestSensorCurrentReadingUpdateRequestDTO implements DeviceRequestDTOInterface
{
    public function __construct(
        private string $sensorName,
        private int $pinNumber,
        private bool $requestedReading,
    ) {}

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    public function getPinNumber(): int
    {
        return $this->pinNumber;
    }

    public function getRequestedReading(): bool
    {
        return $this->requestedReading;
    }
}
