<?php

namespace App\ESPDeviceSensor\DTO\Sensor;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class NewSensorDTO
{
    private ?string $sensorName;

    private int $sensorTypeID;

    private int $deviceNameID;

    public function __construct(
        ?string $sensorName,
        int $sensorTypeID,
        int $deviceNameID,
    ) {
        $this->sensorName = $sensorName;
        $this->sensorTypeID = $sensorTypeID;
        $this->deviceNameID = $deviceNameID;
    }

    /**
     * @return string
     */
    public function getSensorName(): ?string
    {
        return $this->sensorName;
    }

    /**
     * @return int
     */
    public function getSensorTypeID(): int
    {
        return $this->sensorTypeID;
    }

    /**
     * @return int
     */
    public function getDeviceNameID(): int
    {
        return $this->deviceNameID;
    }
}
