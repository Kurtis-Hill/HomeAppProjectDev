<?php

namespace App\Sensors\DTO\Request;

use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use Symfony\Component\Validator\Constraints as Assert;

class SensorUpdateRequestDTO
{
    #[
        Assert\Type(
            type: ['array'],
            message: 'sensorData must be a {{ type }} you have provided {{ value }}'
        ),
        Assert\NotNull(
            message: "sensorData cannot be empty"
        ),
        Assert\Count(
            min: 1,
            max: 50,
            minMessage: "sensorData must contain at least {{ limit }} elements",
            maxMessage: "sensorData cannot contain more than {{ limit }} elements"
        )
    ]
    private mixed $sensorData = null;

    public function getSensorData(): array
    {
        return $this->sensorData;
    }

    public function setSensorData(mixed $sensorData): void
    {
        $this->sensorData = $sensorData;
    }
}
