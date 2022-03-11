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

    public function getSensorData(): mixed
    {
        return $this->sensorData;
    }

    public function setSensorData(mixed $sensorData): void
    {
        $this->sensorData = $sensorData;
    }
}
