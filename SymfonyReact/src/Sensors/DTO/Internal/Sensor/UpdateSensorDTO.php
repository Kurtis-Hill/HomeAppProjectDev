<?php

namespace App\Sensors\DTO\Internal\Sensor;

use App\Devices\Entity\Devices;
use App\Sensors\Entity\Sensor;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class UpdateSensorDTO
{
    private Sensor $sensor;

    private ?string $sensorName;

    private ?Devices $deviceID;

    private ?int $pinNumber;

    public function __construct(
        Sensor $sensor,
        ?string $sensorName = null,
        ?Devices $deviceID = null,
        ?int $pinNumber = null,
    ) {
        $this->sensor = $sensor;
        $this->sensorName = $sensorName;
        $this->deviceID = $deviceID;
        $this->pinNumber = $pinNumber;
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
}
