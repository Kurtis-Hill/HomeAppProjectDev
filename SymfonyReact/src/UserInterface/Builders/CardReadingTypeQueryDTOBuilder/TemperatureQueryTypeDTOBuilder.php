<?php

namespace App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\ReadingTypes;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class TemperatureQueryTypeDTOBuilder implements CardReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): CardSensorTypeJoinQueryDTO
    {
        $tempData = ReadingTypes::SENSOR_READING_TYPE_DATA[Temperature::READING_TYPE];

        return new CardSensorTypeJoinQueryDTO(
            $tempData['alias'],
            $tempData['object'],
            'sensorNameID',
            Sensor::ALIAS,
        );
    }
}
