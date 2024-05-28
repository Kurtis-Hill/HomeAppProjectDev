<?php

namespace App\Sensors\Builders\Request\SensorUpdateBuilders\BoolSensorUpdateBuilder;

use App\Sensors\Builders\Request\SensorUpdateBuilders\SensorReadingUpdateBuilderInterface;
use App\Sensors\DTO\Request\SensorUpdateDTO\BoolSensorUpdateBoundaryDataDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;

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
