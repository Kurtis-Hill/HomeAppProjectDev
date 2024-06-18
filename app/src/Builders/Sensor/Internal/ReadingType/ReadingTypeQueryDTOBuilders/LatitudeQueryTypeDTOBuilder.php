<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders;

use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use JetBrains\PhpStorm\Pure;

class LatitudeQueryTypeDTOBuilder implements ReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $latitudeData = ReadingTypes::SENSOR_READING_TYPE_DATA[Latitude::getReadingTypeName()];

        return new JoinQueryDTO(
            $latitudeData['alias'],
            $latitudeData['object'],
            BaseSensorReadingType::ALIAS,
            'baseReadingTypeID',
            BaseSensorReadingType::ALIAS,
        );
    }
}
