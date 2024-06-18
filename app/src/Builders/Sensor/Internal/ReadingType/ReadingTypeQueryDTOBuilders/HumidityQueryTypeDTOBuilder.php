<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders;

use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use JetBrains\PhpStorm\Pure;

class HumidityQueryTypeDTOBuilder implements ReadingTypeQueryDTOBuilderInterface
{
    #[Pure]
    public function buildReadingTypeJoinQueryDTO(): JoinQueryDTO
    {
        $humidData = ReadingTypes::SENSOR_READING_TYPE_DATA[Humidity::READING_TYPE];

        return new JoinQueryDTO(
            $humidData['alias'],
            $humidData['object'],
            BaseSensorReadingType::ALIAS,
            'baseReadingTypeID',
            BaseSensorReadingType::ALIAS,
        );
    }
}
