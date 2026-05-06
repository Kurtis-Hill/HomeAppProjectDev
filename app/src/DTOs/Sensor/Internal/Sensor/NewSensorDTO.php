<?php

namespace App\DTOs\Sensor\Internal\Sensor;

use App\Entity\Device\Devices;
use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class NewSensorDTO
{
    public function __construct(
        private ?string $sensorName,
        private AbstractSensorType $sensorType,
        private Devices $device,
        private User $user,
        private Sensor $sensor,
        private int $pinNumber,
        private int $readingInterval,
    ) {
    }

    public function getSensorName(): ?string
    {
        return $this->sensorName;
    }

    public function getSensorType(): AbstractSensorType
    {
        return $this->sensorType;
    }

    public function getDevice(): Devices
    {
        return $this->device;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
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
