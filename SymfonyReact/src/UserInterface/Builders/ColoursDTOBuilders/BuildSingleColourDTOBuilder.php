<?php

namespace App\UserInterface\Builders\ColoursDTOBuilders;

use App\UserInterface\DTO\Response\Colours\CardColourDTO;

class BuildSingleColourDTOBuilder
{
    public static function buildSingleColourDTO(
        int $colourID,
        string $cardColour,
        string $shade,
    ): CardColourDTO {
        return new CardColourDTO(
            $colourID,
            $cardColour,
            $shade,
        );
    }

}
