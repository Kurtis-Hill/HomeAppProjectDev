<?php

namespace App\Sensors\Builders\SensorEventUpdateDTOBuilders;

use App\Sensors\DTO\Internal\Event\SensorUpdateEventDTO;
use App\Sensors\Entity\Sensor;

class SensorEventUpdateDTOBuilder
{
    public function buildSensorEventUpdateDTO(Sensor $sensor): SensorUpdateEventDTO
    {
        return new SensorUpdateEventDTO(
            $sensor
        );
    }
}
