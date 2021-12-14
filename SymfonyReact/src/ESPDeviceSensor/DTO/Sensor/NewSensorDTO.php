<?php

namespace App\ESPDeviceSensor\DTO\Sensor;

use App\Devices\Entity\Devices;
use App\Entity\Core\User;
use App\ESPDeviceSensor\Entity\SensorType;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Security\Core\User\UserInterface;

#[Immutable]
class NewSensorDTO
{
    private ?string $sensorName;

    private SensorType $sensorTypeID;

    private Devices $deviceNameID;

    private UserInterface $user;

    public function __construct(
        ?string $sensorName,
        SensorType $sensorType,
        Devices $device,
        UserInterface $user,
    ) {
        $this->sensorName = $sensorName;
        $this->sensorTypeID = $sensorType;
        $this->deviceNameID = $device;
        $this->user = $user;
    }

    public function getSensorName(): ?string
    {
        return $this->sensorName;
    }

    public function getSensorType(): SensorType
    {
        return $this->sensorTypeID;
    }

    public function getDevice(): Devices
    {
        return $this->deviceNameID;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
