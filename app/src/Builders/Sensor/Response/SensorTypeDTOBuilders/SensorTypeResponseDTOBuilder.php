<?php

namespace App\Builders\Sensor\Response\SensorTypeDTOBuilders;

use App\DTOs\Sensor\Response\SensorResponse\SensorTypeResponseDTO;
use App\Entity\Sensor\AbstractSensorType;

class SensorTypeResponseDTOBuilder
{
    public function buildSensorTypeResponseDTO(AbstractSensorType $sensorType): SensorTypeResponseDTO
    {
        return new SensorTypeResponseDTO(
            $sensorType->getSensorTypeID(),
            $sensorType::getReadingTypeName(),
            $sensorType->getDescription()
        );
    }

    public static function buildFullSensorTypeResponseDTO(AbstractSensorType $sensorType): SensorTypeResponseDTO
    {
        return new SensorTypeResponseDTO(
            $sensorType->getSensorTypeID(),
            $sensorType::getReadingTypeName(),
            $sensorType->getDescription()
        );
    }
}
