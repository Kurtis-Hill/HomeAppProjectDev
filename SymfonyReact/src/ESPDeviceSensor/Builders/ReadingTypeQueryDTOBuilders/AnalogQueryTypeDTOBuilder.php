<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\ReadingTypes;
use App\ESPDeviceSensor\Entity\Sensor;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class AnalogQueryTypeDTOBuilder implements ReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $latitudeData = ReadingTypes::SENSOR_READING_TYPE_DATA[Analog::READING_TYPE];

        return new JoinQueryDTO(
            $latitudeData['alias'],
            $latitudeData['object'],
            'sensorNameID',
            Sensor::ALIAS,
        );
    }
}
