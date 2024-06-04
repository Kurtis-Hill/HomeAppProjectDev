<?php

namespace App\Sensors\Builders\Internal\SensorUpdateRequestDTOBuilder;

use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use App\Sensors\Entity\Sensor;

interface SensorUpdateRequestDTOBuilderInterface
{
    public function buildSensorUpdateRequestDTO(Sensor $sensor): SensorUpdateRequestDTOInterface;
}
