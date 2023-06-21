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

    public function __construct(
        Sensor $sensor,
        ?string $sensorName = null,
        ?Devices $deviceID = null,
    ) {
        $this->sensor = $sensor;
        $this->sensorName = $sensorName;
        $this->deviceID = $deviceID;
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
}
