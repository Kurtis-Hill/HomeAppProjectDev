<?php

namespace App\Sensors\Builders\SensorUpdateRequestDTOBuilder;

use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use App\Sensors\Entity\Sensor;

class SingleSensorUpdateRequestDTOBuilder implements SensorUpdateRequestDTOBuilderInterface
{
    public function buildSensorUpdateRequestDTO(Sensor $sensor): SensorUpdateRequestDTOInterface
    {
        return new SingleSensorUpdateRequestDTO(
            $sensor->getSensorName(),
            $sensor->getPinNumber(),
            $sensor->getReadingInterval()
        );
    }
}
