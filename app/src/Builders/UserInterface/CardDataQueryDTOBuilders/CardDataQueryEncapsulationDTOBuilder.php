<?php

namespace App\Builders\UserInterface\CardDataQueryDTOBuilders;

use App\DTOs\UserInterface\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;

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
