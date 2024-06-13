<?php

namespace App\Builders\UserInterface\IconDTOBuilder;

use App\DTOs\UserInterface\Response\Icons\IconResponseDTO;
use App\Entity\UserInterface\Icons;

class IconDTOBuilder
{
    public static function buildIconResponseDTO(Icons $icon): IconResponseDTO
    {
        return new IconResponseDTO(
            $icon->getIconID(),
            $icon->getIconName(),
            $icon->getDescription(),
        );
    }
}
