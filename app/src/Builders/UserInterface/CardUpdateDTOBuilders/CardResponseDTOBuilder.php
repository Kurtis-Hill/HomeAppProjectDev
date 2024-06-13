<?php

namespace App\Builders\UserInterface\CardUpdateDTOBuilders;

use App\Builders\UserInterface\CardStateDTOBuilders\CardStateDTOBuilder;
use App\Builders\UserInterface\ColoursDTOBuilders\ColourDTOBuilder;
use App\Builders\UserInterface\IconDTOBuilder\IconDTOBuilder;
use App\DTOs\UserInterface\Internal\CardUpdateDTO\CardUpdateDTO;
use App\DTOs\UserInterface\Response\CardView\CardViewResponseDTO;
use App\Entity\UserInterface\Card\CardView;

class CardResponseDTOBuilder
{
    public static function buildCardIDUpdateDTO(
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

    public static function buildCardResponseDTO(
        CardView $cardView,
    ): CardViewResponseDTO {
        return new CardViewResponseDTO(
            $cardView->getCardViewID(),
            IconDTOBuilder::buildIconResponseDTO($cardView->getCardIconID()),
            ColourDTOBuilder::buildColourResponseDTO($cardView->getCardColourID()),
            CardStateDTOBuilder::buildCardStateResponseDTO($cardView->getCardStateID()),
        );
    }
}
