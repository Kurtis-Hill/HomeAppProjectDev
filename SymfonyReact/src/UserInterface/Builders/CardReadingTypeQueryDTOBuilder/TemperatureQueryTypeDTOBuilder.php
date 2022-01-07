<?php

namespace App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\ReadingTypes;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class TemperatureQueryTypeDTOBuilder implements ReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $tempData = ReadingTypes::SENSOR_READING_TYPE_DATA[Temperature::READING_TYPE];

        return new JoinQueryDTO(
            $tempData['alias'],
            $tempData['object'],
            'sensorNameID',
            Sensor::ALIAS,
        );
    }
}
