<?php

namespace App\Builders\Sensor\Internal\SensorEventDTOBuilders;

use App\DTOs\Sensor\Internal\Event\SensorUpdateEventDTO;
use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use InvalidArgumentException;

class SensorEventUpdateDTOBuilder
{
    /**
     * @throws InvalidArgumentException
     */
    public function buildSensorUpdateEventDTO(array $sensorUpdateRequestDTOs): SensorUpdateEventDTO
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
