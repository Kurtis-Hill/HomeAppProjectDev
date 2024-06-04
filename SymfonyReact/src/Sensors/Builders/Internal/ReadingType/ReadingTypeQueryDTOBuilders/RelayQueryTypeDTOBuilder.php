<?php

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
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
