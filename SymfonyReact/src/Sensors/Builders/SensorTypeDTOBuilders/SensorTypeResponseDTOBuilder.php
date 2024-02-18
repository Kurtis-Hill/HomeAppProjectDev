<?php

namespace App\Sensors\Builders\SensorTypeDTOBuilders;

use App\Sensors\DTO\Response\SensorResponse\SensorTypeResponseDTO;
use App\Sensors\Entity\SensorType;

class SensorTypeResponseDTOBuilder
{
    public function buildSensorTypeResponseDTO(SensorType $sensorType): SensorTypeResponseDTO
    {
        return new SensorTypeResponseDTO(
            $sensorType->getSensorTypeID(),
            $sensorType->getSensorType(),
            $sensorType->getDescription()
        );
    }

    public static function buildFullSensorTypeResponseDTO(SensorType $sensorType): SensorTypeResponseDTO
    {
        return new SensorTypeResponseDTO(
            $sensorType->getSensorTypeID(),
            $sensorType->getSensorType(),
            $sensorType->getDescription()
        );
    }
}
