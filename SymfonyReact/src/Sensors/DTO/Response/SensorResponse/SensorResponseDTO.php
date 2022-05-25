<?php

namespace App\Sensors\DTO\Response\SensorResponse;

class SensorResponseDTO
{
    private int $sensorNameID;

    private string $sensorName;

    private string $sensorType;

    private string $deviceName;

    private string $createdBy;

    public function __construct(
        int $sensorNameID,
        string $sensorName,
        string $sensorType,
        string $deviceName,
        string $createdBy
    ) {
        $this->sensorNameID = $sensorNameID;
        $this->sensorName = $sensorName;
        $this->sensorType = $sensorType;
        $this->deviceName = $deviceName;
        $this->createdBy = $createdBy;
    }

    public function getSensorNameID(): int
    {
        return $this->sensorNameID;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }
}
