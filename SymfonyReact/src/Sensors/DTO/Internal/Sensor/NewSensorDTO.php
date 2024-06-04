<?php

namespace App\Sensors\DTO\Internal\Sensor;

use App\Devices\Entity\Devices;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\AbstractSensorType;
use App\User\Entity\User;
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
