<?php

namespace App\Sensors\DTO\Request\SendRequests\SensorDataUpdate;

use App\Devices\DTO\Request\DeviceRequest\DeviceRequestDTOInterface;

readonly class BusSensorUpdateRequestDTO implements SensorUpdateRequestDTOInterface, DeviceRequestDTOInterface
{
    public function __construct(
        private array $sensorNames,
        private int $pinNumber,
        private int $sensorCount,
        private int $readingInterval,
    ) {}

    public function getSensorNames(): array
    {
        return $this->sensorNames;
    }

    public function getPinNumber(): int
    {
        return $this->pinNumber;
    }

    public function getSensorCount(): int
    {
        return $this->sensorCount;
    }

    public function getReadingInterval(): int
    {
        return $this->readingInterval;
    }
}
