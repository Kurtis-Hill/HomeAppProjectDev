<?php

namespace App\Sensors\DTO\Request\SendRequests\SensorDataUpdate;

use App\Devices\DTO\Request\DeviceRequest\DeviceRequestDTOInterface;

readonly class RegularSensorUpdateRequestDTO implements SensorUpdateRequestDTOInterface, DeviceRequestDTOInterface
{
    public function __construct(
        private string $sensorName,
        private int $pinNumber,
        private int $readingInterval,
    ) {}

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    public function getPinNumber(): int
    {
        return $this->pinNumber;
    }

    public function getReadingInterval(): int
    {
        return $this->readingInterval;
    }
}
