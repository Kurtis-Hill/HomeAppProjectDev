<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeNotJoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class DallasQueryTypeDTOBuilder implements CardSensorTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildSensorTypeQueryJoinDTO(): CardSensorTypeJoinQueryDTO
    {
        return new CardSensorTypeJoinQueryDTO(
            Dallas::ALIAS,
            Dallas::class,
            'sensorNameID',
            Sensor::ALIAS,
        );
    }

    #[Pure]
    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): CardSensorTypeNotJoinQueryDTO
    {
        return new CardSensorTypeNotJoinQueryDTO(
            Dallas::ALIAS,
            $sensorTypeID
        );
    }
}
