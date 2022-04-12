<?php

namespace App\Sensors\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class SensorUpdateRequestDTO
{
    #[
        Assert\Type(type: ['array'], message: 'sensorData must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "sensorData cannot be empty"
        ),
        Assert\Count(
            min: 1,
            minMessage: "sensorData must contain at least {{ limit }} elements",
        )
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
