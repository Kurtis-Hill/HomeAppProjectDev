<?php

namespace App\DTOs\Sensor\Request;

use App\DTOs\Sensor\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateRequestDTO;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Constraints as Assert;

class SensorUpdateRequestDTO
{
    #[
//        SerializedPath('SensorDataCurrentReadingUpdateDTO[]'),
//        SerializedName('[SensorDataCurrentReadingUpdateDTO]'),
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
        ),
        ArrayShape([SensorDataCurrentReadingUpdateRequestDTO::class])
    ]
    /** @var SensorDataCurrentReadingUpdateRequestDTO[] $sensorData */
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
