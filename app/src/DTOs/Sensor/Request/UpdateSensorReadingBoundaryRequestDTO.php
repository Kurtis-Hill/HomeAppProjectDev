<?php

namespace App\DTOs\Sensor\Request;

use App\DTOs\Sensor\Request\SensorUpdateDTO\BoolSensorUpdateBoundaryDataDTO;
use App\DTOs\Sensor\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;
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

    /**
     * @return StandardSensorUpdateBoundaryDataDTO[]|BoolSensorUpdateBoundaryDataDTO[]
     */
    public function getSensorData(): mixed
    {
        return $this->sensorData;
    }

    public function setSensorData(mixed $sensorData): void
    {
        $this->sensorData = $sensorData;
    }
}
