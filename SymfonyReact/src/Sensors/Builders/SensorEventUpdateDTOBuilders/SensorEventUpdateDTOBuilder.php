<?php

namespace App\Sensors\Builders\SensorEventUpdateDTOBuilders;

use App\Sensors\DTO\Internal\Event\SensorUpdateEventDTO;
use App\Sensors\Entity\Sensor;

class SensorEventUpdateDTOBuilder
{
    public function buildSensorEventUpdateDTO(array $sensors): SensorUpdateEventDTO
    {
        $sensorIDs = [];
        foreach ($sensors as $sensor) {
            if ($sensor instanceof Sensor) {
                $sensorIDs[] = $sensor->getSensorID();
            }
            if (is_int($sensor)) {
                $sensorIDs[] = $sensor;
            }
        }

        return new SensorUpdateEventDTO(
            $sensorIDs,
        );
    }
}
