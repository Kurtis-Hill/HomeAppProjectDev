<?php

namespace App\UserInterface\Builders\CardStateBuilders;

use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\Entity\Card\Cardstate;

class CardStateBuilder
{
    public static function buildCardStateResponseDTO(Cardstate $cardState): CardStateResponseDTO
    {
        return new CardStateResponseDTO(
            $cardState->getCardstateID(),
            $cardState->getState(),
        );
    }
}
