<?php

namespace App\Sensors\Builders\SensorUpdateBuilders\StandardSensorUpdateBuilder;

use App\Sensors\Builders\SensorUpdateBuilders\SensorReadingUpdateBuilderInterface;
use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;
use App\Sensors\DTO\Request\SensorUpdateDTO\StandardSensorUpdateBoundaryDataDTO;

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
