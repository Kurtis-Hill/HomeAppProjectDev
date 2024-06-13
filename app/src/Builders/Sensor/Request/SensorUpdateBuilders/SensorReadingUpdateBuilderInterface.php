<?php

namespace App\Builders\Sensor\Request\SensorUpdateBuilders;

use App\DTOs\Sensor\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;

interface SensorReadingUpdateBuilderInterface
{
    public function buildSensorTypeDTO(array $sensorData): SensorUpdateBoundaryDataDTOInterface;
}
