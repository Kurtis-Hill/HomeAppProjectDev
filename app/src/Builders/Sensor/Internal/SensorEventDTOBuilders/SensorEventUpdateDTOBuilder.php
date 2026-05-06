<?php

declare (strict_types = 1);

namespace App\Builders\Sensor\Internal\SensorEventDTOBuilders;

use App\DTOs\Sensor\Internal\Event\SensorUpdateEventDTO;

class SensorEventUpdateDTOBuilder
{
    public function buildSensorUpdateEventDTO(int $sensorID): SensorUpdateEventDTO
    {
        return new SensorUpdateEventDTO(
            $sensorID,
        );
    }
}
