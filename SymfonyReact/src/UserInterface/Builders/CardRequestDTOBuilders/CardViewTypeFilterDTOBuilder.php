<?php

namespace App\UserInterface\Builders\CardRequestDTOBuilders;

use App\Devices\Entity\Devices;
use App\User\Entity\Room;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardDataPreFilterDTO;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardViewUriFilterDTO;

class CardViewTypeFilterDTOBuilder
{
    public static function buildCardViewTypeFilterDTO(
        ?Room $cardViewType = null,
        ?Devices $cardViewTypeFilter = null,
    ): CardViewUriFilterDTO {
        return new CardViewUriFilterDTO(
            $cardViewType,
            $cardViewTypeFilter,
        );
    }

    public static function buildCardDataPreFilterDTO(
        array $sensorTypes,
        array $readingTypes
    ): CardDataPreFilterDTO {
        return new CardDataPreFilterDTO(
            $sensorTypes,
            $readingTypes,
        );
    }
}
