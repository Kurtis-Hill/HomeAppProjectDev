<?php

namespace App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\ReadingTypes;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\Sensor;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class HumidityQueryTypeDTOBuilder implements CardReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $humidData = ReadingTypes::SENSOR_READING_TYPE_DATA[Humidity::READING_TYPE];

        return new JoinQueryDTO(
            $humidData['alias'],
            $humidData['object'],
            'sensorNameID',
            Sensor::ALIAS,
        );
    }
}