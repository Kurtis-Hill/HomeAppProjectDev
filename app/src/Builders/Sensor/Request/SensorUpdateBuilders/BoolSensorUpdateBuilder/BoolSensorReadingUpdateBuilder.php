<?php

namespace App\Builders\Sensor\Request\SensorUpdateBuilders\BoolSensorUpdateBuilder;

use App\Builders\Sensor\Request\SensorUpdateBuilders\SensorReadingUpdateBuilderInterface;
use App\DTOs\Sensor\Request\SensorUpdateDTO\BoolSensorUpdateBoundaryDataDTO;
use App\DTOs\Sensor\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;

class BoolSensorReadingUpdateBuilder implements SensorReadingUpdateBuilderInterface
{
    public function buildSensorTypeDTO(array $sensorData): SensorUpdateBoundaryDataDTOInterface
    {
        return new BoolSensorUpdateBoundaryDataDTO(
            $sensorData['readingType'],
            $sensorData['expectedReading'] ?? null,
            $sensorData['constRecord'] ?? null,
        );
    }
}
