<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeNotJoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class BmpQueryTypeDTOBuilder implements CardSensorTypeQueryDTOBuilder
{
    #[Pure]
    public function buildSensorTypeQueryDTOSensorNameJoin(): CardSensorTypeJoinQueryDTO
    {
        return new CardSensorTypeJoinQueryDTO(
            Bmp::ALIAS,
            Bmp::class,
            'sensorNameID',
            Sensor::ALIAS,
        );
    }

    #[Pure]
    public function buildSensorTypeQueryExcludeSensorDTO(int $sensorTypeID): CardSensorTypeNotJoinQueryDTO
    {
        return new CardSensorTypeNotJoinQueryDTO(
            Bmp::ALIAS,
            $sensorTypeID
        );
    }
}
