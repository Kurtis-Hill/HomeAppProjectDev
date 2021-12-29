<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeNotJoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class SoilQueryTypeDTOBuilder implements CardSensorTypeQueryDTOBuilder
{
    #[Pure]
    public function buildSensorTypeQueryDTOSensorNameJoin(): CardSensorTypeJoinQueryDTO
    {
        return new CardSensorTypeJoinQueryDTO(
            Soil::ALIAS,
            Soil::class,
            'sensorNameID',
            Sensor::ALIAS,
        );
    }

    #[Pure]
    public function buildSensorTypeQueryExcludeSensorDTO(int $sensorTypeID): CardSensorTypeNotJoinQueryDTO
    {
        return new CardSensorTypeNotJoinQueryDTO(
            Soil::ALIAS,
            $sensorTypeID
        );
    }
}
