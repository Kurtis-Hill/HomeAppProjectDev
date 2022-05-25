<?php

namespace App\Sensors\Builders\SensorResponseBuilders;

use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\Sensor;

class SensorResponseBuilder
{
    public static function buildSensorResponseDTO(Sensor $sensor): SensorResponseDTO
    {
        return new SensorResponseDTO(
            $sensor->getSensorNameID(),
            $sensor->getSensorName(),
            $sensor->getSensorTypeObject()->getSensorType(),
            $sensor->getDeviceObject()->getDeviceName(),
            $sensor->getCreatedBy()->getUsername(),
        );
    }
}
