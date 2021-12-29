<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeNotJoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class DHTQueryTypeDTOBuilder implements CardSensorTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildSensorTypeQueryJoinDTO(): CardSensorTypeJoinQueryDTO
    {
        return new CardSensorTypeJoinQueryDTO(
            Dht::ALIAS,
            Dht::class,
            'sensorNameID',
            Sensor::ALIAS,
        );
    }

    #[Pure]
    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): CardSensorTypeNotJoinQueryDTO
    {
        return new CardSensorTypeNotJoinQueryDTO(
            Dht::ALIAS,
            $sensorTypeID
        );
    }
}
