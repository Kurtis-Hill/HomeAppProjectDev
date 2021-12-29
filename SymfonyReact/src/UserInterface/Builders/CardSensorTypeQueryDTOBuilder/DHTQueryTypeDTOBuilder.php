<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeQueryDTO;
use JetBrains\PhpStorm\Pure;

class DHTQueryTypeDTOBuilder implements CardSensorTypeQueryDTOBuilder
{
    #[Pure]
    public function buildSensorTypeQueryDTO(): CardSensorTypeQueryDTO
    {
        return new CardSensorTypeQueryDTO(
            'dht',
            Dht::class,
            ['sensors', 'sensorNameID']
        );
    }
}
