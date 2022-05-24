<?php

namespace App\UserInterface\Builders\CardUpdateBuilders;

use App\UserInterface\DTO\Internal\CardUpdateDTO\CardUpdateDTO;

class CardUpdateBuilder
{
    public static function buildCardUpdateDTO(
        ?int $cardColour,
        ?int $cardIcon,
        ?int $cardViewState,
    ): CardUpdateDTO {
        return new CardUpdateDTO(
            $cardColour,
            $cardIcon,
            $cardViewState,
        );
    }
}
