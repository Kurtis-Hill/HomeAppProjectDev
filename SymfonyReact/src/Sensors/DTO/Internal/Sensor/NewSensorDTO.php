<?php

namespace App\Sensors\DTO\Internal\Sensor;

use App\Devices\Entity\Devices;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Security\Core\User\UserInterface;

#[Immutable]
readonly class NewSensorDTO
{
    public function __construct(
        private ?string $sensorName,
        private SensorType $sensorType,
        private Devices $device,
        private UserInterface $user,
        private Sensor $sensor,
        private int $pinNumber,
    ) {
    }

    public function getSensorName(): ?string
    {
        return $this->sensorName;
    }

    public function getSensorType(): SensorType
    {
        return $this->sensorType;
    }

    public function getDevice(): Devices
    {
        return $this->device;
    }

    public function getUser(): UserInterface
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
}
