<?php

namespace App\Builders\UserInterface\CardRequestDTOBuilders;

use App\DTOs\UserInterface\Internal\CardDataFiltersDTO\CardViewUriFilterDTO;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\Room;

class CardViewTypeFilterDTOBuilder
{
    public static function buildCardViewTypeFilterDTO(
        ?Room $cardViewType = null,
        ?Devices $cardViewTypeFilter = null,
        ?Group $cardViewTypeFilterGroup = null,
    ): CardViewUriFilterDTO {
        return new CardViewUriFilterDTO(
            $cardViewType,
            $cardViewTypeFilter,
            $cardViewTypeFilterGroup,
        );
    }
}
