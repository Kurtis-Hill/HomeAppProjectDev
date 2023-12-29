<?php

namespace App\Sensors\DTO\Request;

use App\Sensors\DTO\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateDTO;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Annotation\SerializedPath;
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
        ArrayShape([SensorDataCurrentReadingUpdateDTO::class])
    ]
    /** @var $sensorData SensorDataCurrentReadingUpdateDTO[] */
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
