<?php

namespace App\Sensors\Builders\ReadingTypeQueryDTOBuilders;

use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Entity\Sensor;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
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
            'sensor',
            'sensorID',
            Sensor::ALIAS,
        );
    }
}
