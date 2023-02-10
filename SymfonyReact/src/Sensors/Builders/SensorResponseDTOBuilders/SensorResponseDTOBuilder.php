<?php

namespace App\Sensors\Builders\SensorResponseDTOBuilders;

use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\Sensor;

class SensorResponseDTOBuilder
{
    public static function buildSensorResponseDTO(Sensor $sensor): SensorResponseDTO
    {
        return new SensorResponseDTO(
            $sensor->getSensorID(),
            $sensor->getSensorName(),
            $sensor->getSensorTypeObject()->getSensorType(),
            $sensor->getDevice()->getDeviceName(),
            $sensor->getCreatedBy()->getUsername(),
        );
    }
}
