<?php

namespace App\UserInterface\Builders\CardStateDTOBuilders;

use App\UserInterface\DTO\Response\State\StateResponseDTO;
use App\UserInterface\Entity\Card\Cardstate;

class CardStateDTOBuilder
{
    public static function buildCardStateResponseDTO(Cardstate $cardState): StateResponseDTO
    {
        return new StateResponseDTO(
            $cardState->getStateID(),
            $cardState->getState(),
        );
    }
}
