<?php

namespace App\Sensors\Builders\SensorEventUpdateDTOBuilders;

use App\Sensors\DTO\Internal\Event\SensorUpdateEventDTO;
use App\Sensors\Entity\Sensor;

class SensorEventUpdateDTOBuilder
{
    public function buildSensorEventUpdateDTO(Sensor|int $sensor): SensorUpdateEventDTO
    {
        $sensorID = $sensor instanceof Sensor
            ? $sensor->getSensorID()
            : $sensor;

        return new SensorUpdateEventDTO(
            $sensorID,
        );
    }
}
