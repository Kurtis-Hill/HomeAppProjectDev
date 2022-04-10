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
    ]
    private mixed $sensorData = null;

//    #[
//        Assert\Type(type: ['string'], message: 'sensorType must be a {{ type }} you have provided {{ value }}'),
//        Assert\NotNull(
//            message: "sensorType must be provided"
//        ),
//    ]
//    private mixed $sensorType = null;

    public function getSensorData(): mixed
    {
        return $this->sensorData;
    }

    public function setSensorData(mixed $sensorData): void
    {
        $this->sensorData = $sensorData;
    }

//    public function getSensorType(): mixed
//    {
//        return $this->sensorType;
//    }
//
//    public function setSensorType(mixed $sensorType): void
//    {
//        $this->sensorType = $sensorType;
//    }
}
