<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders;

use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use JetBrains\PhpStorm\Pure;

class MotionQueryTypeDTOBuilder implements ReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $motionData = ReadingTypes::SENSOR_READING_TYPE_DATA[Motion::READING_TYPE];

        return new JoinQueryDTO(
            $motionData['alias'],
            $motionData['object'],
            BaseSensorReadingType::ALIAS,
            'baseReadingTypeID',
            BaseSensorReadingType::ALIAS,
        );
    }
}
