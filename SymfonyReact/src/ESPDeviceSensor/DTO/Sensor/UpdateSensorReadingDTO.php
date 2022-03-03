<?php

namespace App\ESPDeviceSensor\DTO\Sensor;

class UpdateSensorReadingDTO
{
    /**
     * @var string
     */
    private string $sensorType;

    /**
     * @var string
     */
    private string $sensorName;

    /**
     * @var array
     */
    private array $sensorData;

    /**
     * @var int
     */
    private int $deviceId;


    /**
     * @param string $sensorType
     * @param string $sensorName
     * @param array $currentReadings
     * @param int $deviceId
     */
    public function __construct(
        string $sensorType,
        string $sensorName,
        array $currentReadings,
        int $deviceId,
    )
    {
        $this->sensorType = $sensorType;
        $this->sensorData = $currentReadings;
        $this->deviceId = $deviceId;
        $this->sensorName = $sensorName;
    }

    /**
     * @return string
     */
    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    /**
     * @return array
     */
    public function getCurrentReadings(): array
    {
        return $this->sensorData;
    }

    /**
     * @return int
     */
    public function getDeviceId(): int
    {
        return $this->deviceId;
    }

    /**
     * @return string
     */
    public function getSensorName(): string
    {
        return $this->sensorName;
    }
}
