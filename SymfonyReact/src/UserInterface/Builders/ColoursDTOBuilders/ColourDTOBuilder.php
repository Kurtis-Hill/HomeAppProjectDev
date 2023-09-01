<?php

namespace App\UserInterface\Builders\ColoursDTOBuilders;

use App\UserInterface\DTO\Response\Colours\ColourResponseDTO;
use App\UserInterface\Entity\Card\CardColour;

class ColourDTOBuilder
{
    public static function buildColourResponseDTO(
        CardColour $cardColour
    ): ColourResponseDTO {
        return new ColourResponseDTO(
            $cardColour->getColourID(),
            $cardColour->getColour(),
            $cardColour->getShade(),
        );
    }

}
