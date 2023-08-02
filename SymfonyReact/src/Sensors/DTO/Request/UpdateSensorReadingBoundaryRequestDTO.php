<?php

namespace App\Sensors\DTO\Request;

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
        Assert\Range(
            notInRangeMessage: "readingInterval must be between {{ min }} and {{ max }}",
            invalidMessage: "readingInterval must be a number",
            min: 500,
            max: 100000
        ),
//        Assert\Type(type: ['int', 'null'], message: 'readingInterval must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $readingInterval = null;

    public function getSensorData(): mixed
    {
        return $this->sensorData;
    }

    public function setSensorData(mixed $sensorData): void
    {
        $this->sensorData = $sensorData;
    }

    public function getReadingInterval(): mixed
    {
        return $this->readingInterval;
    }

    public function setReadingInterval(mixed $readingInterval): void
    {
        $this->readingInterval = $readingInterval;
    }
}
