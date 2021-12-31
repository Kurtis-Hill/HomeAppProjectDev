<?php

namespace App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\ReadingTypes;
use App\ESPDeviceSensor\Entity\Sensor;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class LatitudeQueryTypeDTOBuilder implements CardReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $latitudeData = ReadingTypes::SENSOR_READING_TYPE_DATA[ReadingTypes\Latitude::READING_TYPE];

        return new JoinQueryDTO(
            $latitudeData['alias'],
            $latitudeData['object'],
            'sensorNameID',
            Sensor::ALIAS,
        );
    }
}
