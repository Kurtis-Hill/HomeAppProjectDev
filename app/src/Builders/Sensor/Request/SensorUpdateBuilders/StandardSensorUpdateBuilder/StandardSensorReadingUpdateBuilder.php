<?php

namespace App\Builders\Sensor\Request\SensorUpdateBuilders\StandardSensorUpdateBuilder;

use App\Builders\Sensor\Request\SensorUpdateBuilders\SensorReadingUpdateBuilderInterface;
use App\DTOs\Sensor\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\DTOs\Sensor\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;

class StandardSensorReadingUpdateBuilder implements SensorReadingUpdateBuilderInterface
{
    public function buildSensorTypeDTO(array $sensorData): SensorUpdateBoundaryDataDTOInterface
    {
        return new StandardSensorUpdateBoundaryDataDTO(
            $sensorData['readingType'],
            $sensorData['highReading'] ?? null,
            $sensorData['lowReading'] ?? null,
            $sensorData['constRecord'] ?? null,
        );
    }
}
