<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders;

use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
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
            BaseSensorReadingType::ALIAS,
            'baseReadingTypeID',
            BaseSensorReadingType::ALIAS,
        );
    }
}
