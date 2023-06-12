<?php

namespace App\UserInterface\Builders\CardUpdateDTOBuilders;

use App\UserInterface\Builders\CardStateDTOBuilders\CardStateDTOBuilder;
use App\UserInterface\Builders\ColoursDTOBuilders\ColourDTOBuilder;
use App\UserInterface\Builders\IconDTOBuilder\IconDTOBuilder;
use App\UserInterface\DTO\Internal\CardUpdateDTO\CardUpdateDTO;
use App\UserInterface\DTO\Response\CardView\CardViewResponseDTO;
use App\UserInterface\Entity\Card\CardView;

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
