<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders;

use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\ReadingTypes;
use App\ESPDeviceSensor\Entity\Sensor;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class LatitudeQueryTypeDTOBuilder implements ReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $latitudeData = ReadingTypes::SENSOR_READING_TYPE_DATA[Latitude::READING_TYPE];

        return new JoinQueryDTO(
            $latitudeData['alias'],
            $latitudeData['object'],
            'sensorNameID',
            Sensor::ALIAS,
        );
    }
}
