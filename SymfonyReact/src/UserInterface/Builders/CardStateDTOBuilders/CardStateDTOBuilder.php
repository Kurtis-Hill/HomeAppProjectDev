<?php

namespace App\UserInterface\Builders\CardStateDTOBuilders;

use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\Entity\Card\Cardstate;

class CardStateDTOBuilder
{
    public static function buildCardStateResponseDTO(Cardstate $cardState): CardStateResponseDTO
    {
        return new CardStateResponseDTO(
            $cardState->getStateID(),
            $cardState->getState(),
        );
    }
}
