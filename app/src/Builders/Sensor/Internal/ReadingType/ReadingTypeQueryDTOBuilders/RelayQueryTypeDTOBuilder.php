<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders;

use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use JetBrains\PhpStorm\Pure;

class RelayQueryTypeDTOBuilder implements ReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $relayData = ReadingTypes::SENSOR_READING_TYPE_DATA[Relay::READING_TYPE];

        return new JoinQueryDTO(
            $relayData['alias'],
            $relayData['object'],
            BaseSensorReadingType::ALIAS,
            'baseReadingTypeID',
            BaseSensorReadingType::ALIAS,
        );
    }
}
