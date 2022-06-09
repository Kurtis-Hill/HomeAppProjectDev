<?php

namespace App\UserInterface\Builders\IconDTOBuilder;

use App\UserInterface\DTO\Response\Icons\IconResponseDTO;
use App\UserInterface\Entity\Icons;

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
