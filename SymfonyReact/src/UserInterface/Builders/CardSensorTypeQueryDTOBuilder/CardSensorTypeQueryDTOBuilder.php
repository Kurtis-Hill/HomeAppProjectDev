<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\SensorType;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeNotJoinQueryDTO;

interface CardSensorTypeQueryDTOBuilder
{
    public function buildSensorTypeQueryDTOSensorNameJoin(): CardSensorTypeJoinQueryDTO;

    public function buildSensorTypeQueryExcludeSensorDTO(int $sensorTypeID): CardSensorTypeNotJoinQueryDTO;
}
