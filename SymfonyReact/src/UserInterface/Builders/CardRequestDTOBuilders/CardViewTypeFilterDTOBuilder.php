<?php

namespace App\UserInterface\Builders\CardRequestDTOBuilders;

use App\Devices\Entity\Devices;
use App\User\Entity\Room;
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
}
