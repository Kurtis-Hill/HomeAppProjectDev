<?php

namespace App\Sensors\Builders\SensorUpdateBuilders;

use App\Devices\Entity\Devices;
use App\Sensors\DTO\Internal\Sensor\UpdateSensorDTO;
use App\Sensors\Entity\Sensor;

class SensorUpdateDTOBuilder
{
    public static function buildSensorUpdateDTO(
        Sensor $sensor,
        ?string $sensorName = null,
        ?Devices $deviceID = null,
        ?int $pinNumber = null,
        ?int $readingInterval = null,
    ): UpdateSensorDTO {
        return new UpdateSensorDTO(
            $sensor,
            $sensorName,
            $deviceID,
            $pinNumber,
            $readingInterval,
        );
    }
}
