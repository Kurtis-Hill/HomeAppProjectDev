<?php

namespace App\ESPDeviceSensor\DTO\Sensor;

class UpdateSensorReadingBoundaryIDDTO
{
    private string $sensorType;

    private int $sensorReadingBoundaryID;

    public function __construct(string $sensorType, int $sensorReadingBoundaryID)
    {
        $this->sensorType = $sensorType;
        $this->sensorReadingBoundaryID = $sensorReadingBoundaryID;
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    public function getSensorReadingBoundaryID(): int
    {
        return $this->sensorReadingBoundaryID;
    }
}
