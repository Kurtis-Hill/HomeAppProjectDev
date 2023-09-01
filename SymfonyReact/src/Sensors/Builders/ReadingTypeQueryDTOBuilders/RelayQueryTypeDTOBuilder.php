<?php

namespace App\Sensors\Builders\ReadingTypeQueryDTOBuilders;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Entity\Sensor;
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
            'sensor',
            'sensorID',
            Sensor::ALIAS,
        );
    }
}
