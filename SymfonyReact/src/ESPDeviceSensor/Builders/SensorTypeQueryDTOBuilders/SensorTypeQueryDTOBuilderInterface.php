<?php

namespace App\ESPDeviceSensor\Builders\SensorTypeQueryDTOBuilders;

use App\ESPDeviceSensor\Entity\SensorType;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;

interface SensorTypeQueryDTOBuilderInterface
{
    public function buildSensorTypeQueryJoinDTO(): JoinQueryDTO;

    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): SensorTypeNotJoinQueryDTO;
}
