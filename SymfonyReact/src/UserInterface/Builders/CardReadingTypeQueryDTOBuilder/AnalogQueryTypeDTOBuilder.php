<?php

namespace App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder;

use App\ESPDeviceSensor\Entity\ReadingTypes;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\Sensor;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class AnalogQueryTypeDTOBuilder implements CardReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): CardSensorTypeJoinQueryDTO
    {
        $latitudeData = ReadingTypes::SENSOR_READING_TYPE_DATA[Analog::READING_TYPE];

        return new CardSensorTypeJoinQueryDTO(
            $latitudeData['alias'],
            $latitudeData['object'],
            'sensorNameID',
            Sensor::ALIAS,
        );
    }
}
