<?php

namespace App\Sensors\Builders\ReadingTypeQueryDTOBuilders;

use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Entity\Sensor;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use JetBrains\PhpStorm\Pure;

class LatitudeQueryTypeDTOBuilder implements ReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $latitudeData = ReadingTypes::SENSOR_READING_TYPE_DATA[Latitude::READING_TYPE];

        return new JoinQueryDTO(
            $latitudeData['alias'],
            $latitudeData['object'],
            'sensorNameID',
            Sensor::ALIAS,
        );
    }
}
