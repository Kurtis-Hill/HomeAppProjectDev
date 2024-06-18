<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders;

use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
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
            BaseSensorReadingType::ALIAS,
            'baseReadingTypeID',
            BaseSensorReadingType::ALIAS,
        );
    }
}
