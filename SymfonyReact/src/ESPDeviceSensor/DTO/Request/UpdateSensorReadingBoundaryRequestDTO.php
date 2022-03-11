<?php

namespace App\ESPDeviceSensor\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateSensorReadingBoundaryRequestDTO
{
    #[
        Assert\Type(type: 'array', message: 'sensorData must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "sensorData cannot be empty"
        ),
    ]
    private mixed $sensorData = null;

    #[
        Assert\Type(type: 'int', message: 'sensorId must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "sensorId cannot be null"
        ),
    ]
    private mixed $sensorId = null;

    public function getSensorData(): mixed
    {
        return $this->sensorData;
    }

    public function setSensorData(mixed $sensorData): void
    {
        $this->sensorData = $sensorData;
    }

    public function getSensorId(): mixed
    {
        return $this->sensorId;
    }

    public function setSensorId(mixed $sensorId): void
    {
        $this->sensorId = $sensorId;
    }
}
