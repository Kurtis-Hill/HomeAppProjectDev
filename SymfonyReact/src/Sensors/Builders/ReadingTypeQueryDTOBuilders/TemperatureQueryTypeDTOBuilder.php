<?php

namespace App\Sensors\Builders\ReadingTypeQueryDTOBuilders;

use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class TemperatureQueryTypeDTOBuilder implements ReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $tempData = ReadingTypes::SENSOR_READING_TYPE_DATA[Temperature::getReadingTypeName()];

        return new JoinQueryDTO(
            $tempData['alias'],
            $tempData['object'],
            'sensor',
            Sensor::ALIAS,
        );
    }
}
