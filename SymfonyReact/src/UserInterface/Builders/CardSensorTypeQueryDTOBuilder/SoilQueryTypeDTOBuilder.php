<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeQueryDTO;
use JetBrains\PhpStorm\Pure;

class SoilQueryTypeDTOBuilder implements CardSensorTypeQueryDTOBuilder
{
    #[Pure]
    public function buildSensorTypeQueryDTO(): CardSensorTypeQueryDTO
    {
        return new CardSensorTypeQueryDTO(
            'soil',
            Soil::class,
            ['sensors', 'sensorNameID']
        );
    }

}
