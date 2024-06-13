<?php

namespace App\Builders\Sensor\Internal\SensorUpdateRequestDTOBuilder;

use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use App\Entity\Sensor\Sensor;

interface SensorUpdateRequestDTOBuilderInterface
{
    public function buildSensorUpdateRequestDTO(Sensor $sensor): SensorUpdateRequestDTOInterface;
}
