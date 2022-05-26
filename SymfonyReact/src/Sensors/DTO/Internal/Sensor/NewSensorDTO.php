<?php

namespace App\Sensors\DTO\Internal\Sensor;

use App\Devices\Entity\Devices;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Security\Core\User\UserInterface;

#[Immutable]
class NewSensorDTO
{
    private ?string $sensorName;

    private SensorType $sensorTypeID;

    private Devices $deviceNameID;

    private UserInterface $user;

    private Sensor $sensor;

    public function __construct(
        ?string $sensorName,
        SensorType $sensorType,
        Devices $device,
        UserInterface $user,
        Sensor $sensor,
    ) {
        $this->sensorName = $sensorName;
        $this->sensorTypeID = $sensorType;
        $this->deviceNameID = $device;
        $this->user = $user;
        $this->sensor = $sensor;
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

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }
}
