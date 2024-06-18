<?php

namespace App\Builders\UserInterface\ColoursDTOBuilders;

use App\DTOs\UserInterface\Response\Colours\ColourResponseDTO;
use App\Entity\UserInterface\Card\Colour;

class ColourDTOBuilder
{
    public static function buildColourResponseDTO(
        Colour $cardColour
    ): ColourResponseDTO {
        return new ColourResponseDTO(
            $cardColour->getColourID(),
            $cardColour->getColour(),
            $cardColour->getShade(),
        );
    }

}
