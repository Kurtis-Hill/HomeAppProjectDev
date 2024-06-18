<?php

namespace App\Builders\UserInterface\CardStateDTOBuilders;

use App\DTOs\UserInterface\Response\State\StateResponseDTO;
use App\Entity\UserInterface\Card\CardState;

class CardStateDTOBuilder
{
    public static function buildCardStateResponseDTO(CardState $cardState): StateResponseDTO
    {
        return new StateResponseDTO(
            $cardState->getStateID(),
            $cardState->getState(),
        );
    }
}
