<?php

namespace App\Sensors\Builders\SensorEventUpdateDTOBuilders;

use App\Sensors\DTO\Internal\Event\SensorUpdateEventDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use InvalidArgumentException;

class SensorEventUpdateDTOBuilder
{
    /**
     * @throws InvalidArgumentException
     */
    public function buildSensorEventUpdateDTO(array $sensorUpdateRequestDTOs): SensorUpdateEventDTO
    {
        $validatedDTOs = [];
        foreach ($sensorUpdateRequestDTOs as $sensor) {
            if (!$sensor instanceof SensorUpdateRequestDTOInterface) {
                throw new InvalidArgumentException('Sensor must implement SensorUpdateRequestDTOInterface');
            }

            $validatedDTOs[] = $sensor;
        }

        return new SensorUpdateEventDTO(
            $validatedDTOs,
        );
    }
}
