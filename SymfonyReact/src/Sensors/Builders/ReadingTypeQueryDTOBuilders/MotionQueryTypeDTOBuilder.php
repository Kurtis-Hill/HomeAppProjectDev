<?php

namespace App\Sensors\Builders\ReadingTypeQueryDTOBuilders;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Entity\Sensor;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
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
            'sensor',
            'sensorID',
            Sensor::ALIAS,
        );
    }
}
