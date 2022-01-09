<?php

namespace App\ESPDeviceSensor\Builders\SensorTypeQueryDTOBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\DTO\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class DallasQueryTypeDTOBuilder implements SensorTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildSensorTypeQueryJoinDTO(): JoinQueryDTO
    {
        return new JoinQueryDTO(
            Dallas::ALIAS,
            Dallas::class,
            'sensorNameID',
            Sensor::ALIAS,
        );
    }

    #[Pure]
    public function buildSensorTypeQueryExcludeDTO(int $sensorTypeID): SensorTypeNotJoinQueryDTO
    {
        return new SensorTypeNotJoinQueryDTO(
            Dallas::ALIAS,
            $sensorTypeID
        );
    }
}
