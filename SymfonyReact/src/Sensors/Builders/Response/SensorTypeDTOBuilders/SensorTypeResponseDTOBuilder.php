<?php

namespace App\Sensors\Builders\Response\SensorTypeDTOBuilders;

use App\Sensors\DTO\Response\SensorResponse\SensorTypeResponseDTO;
use App\Sensors\Entity\AbstractSensorType;

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
