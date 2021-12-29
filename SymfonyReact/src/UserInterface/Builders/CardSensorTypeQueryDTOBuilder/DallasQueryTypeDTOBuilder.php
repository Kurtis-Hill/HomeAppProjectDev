<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeQueryDTO;
use JetBrains\PhpStorm\Pure;

class DallasQueryTypeDTOBuilder implements CardSensorTypeQueryDTOBuilder
{
    #[Pure]
    public function buildSensorTypeQueryDTO(): CardSensorTypeQueryDTO
    {
        return new CardSensorTypeQueryDTO(
            'dallas',
            Dallas::class,
            ['sensors', 'sensorNameID']
        );
    }
}
