<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\SensorType;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeNotJoinQueryDTO;

interface CardSensorTypeQueryDTOBuilderInterface
{
    public function buildSensorTypeQueryJoinDTO(): CardSensorTypeJoinQueryDTO;

    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): CardSensorTypeNotJoinQueryDTO;
}
