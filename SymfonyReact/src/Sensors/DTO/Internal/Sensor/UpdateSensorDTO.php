<?php

namespace App\Sensors\DTO\Internal\Sensor;

use App\Devices\Entity\Devices;
use App\Sensors\Entity\Sensor;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class UpdateSensorDTO
{
    public function __construct(
        private Sensor $sensor,
        private ?string $sensorName = null,
        private ?Devices $deviceID = null,
        private ?int $pinNumber = null,
        private ?int $readingInterval = null,
    ) {
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function getSensorName(): ?string
    {
        return $this->sensorName;
    }

    public function getDeviceID(): ?Devices
    {
        return $this->deviceID;
    }

    public function getPinNumber(): ?int
    {
        return $this->pinNumber;
    }

    public function getReadingInterval(): ?int
    {
        return $this->readingInterval;
    }
}
