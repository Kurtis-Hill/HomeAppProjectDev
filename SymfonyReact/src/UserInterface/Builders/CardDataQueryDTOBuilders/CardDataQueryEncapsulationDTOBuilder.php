<?php

namespace App\UserInterface\Builders\CardDataQueryDTOBuilders;

use App\UserInterface\DTO\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;

class CardDataQueryEncapsulationDTOBuilder
{
    public static function buildCardDAtaQueryEncapsulationDTO(
        array $sensorTypesToQuery = [],
        array $sensorTypesToExclude = [],
        array $readingTypesToQuery = [],
    ): CardDataQueryEncapsulationFilterDTO {
        return new CardDataQueryEncapsulationFilterDTO(
            $sensorTypesToQuery,
            $sensorTypesToExclude,
            $readingTypesToQuery,
        );
    }
}
