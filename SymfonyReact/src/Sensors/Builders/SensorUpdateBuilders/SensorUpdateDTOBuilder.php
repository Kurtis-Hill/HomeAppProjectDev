<?php

namespace App\Sensors\Builders\SensorUpdateBuilders;

use App\Devices\Entity\Devices;
use App\Sensors\DTO\Internal\Sensor\UpdateSensorDTO;
use App\Sensors\Entity\Sensor;

class SensorUpdateDTOBuilder
{
    public static function buildSensorUpdateDTO(
        Sensor $sensor,
        ?string $sensorName,
        ?Devices $deviceID,
    ): UpdateSensorDTO {
        return new UpdateSensorDTO(
            $sensor,
            $sensorName,
            $deviceID,
        );
    }
}
