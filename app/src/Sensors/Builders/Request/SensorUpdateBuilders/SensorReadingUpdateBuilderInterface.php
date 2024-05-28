<?php

namespace App\Sensors\Builders\Request\SensorUpdateBuilders;

use App\Sensors\DTO\Request\SensorUpdateDTO\SensorUpdateBoundaryDataDTOInterface;

interface SensorReadingUpdateBuilderInterface
{
    public function buildSensorTypeDTO(array $sensorData): SensorUpdateBoundaryDataDTOInterface;
}
