<?php

namespace App\UserInterface\Builders\ColoursDTOBuilders;

use App\UserInterface\DTO\Response\Colours\CardColourResponseDTO;
use App\UserInterface\Entity\Card\CardColour;

class ColourDTOBuilder
{
    public static function buildColourResponseDTO(
        CardColour $cardColour
    ): CardColourResponseDTO {
        return new CardColourResponseDTO(
            $cardColour->getColourID(),
            $cardColour->getColour(),
            $cardColour->getShade(),
        );
    }

}
