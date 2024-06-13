<?php

namespace App\Builders\UserInterface\NavBarDTOBuilders;

use App\DTOs\UserInterface\Response\NavBar\NavBarListLinkDTO;

class NavBarListLinkDTOBuilder
{
    public static function buildNavBarListLinkDTO(
        string $displayName,
        string $link,
    ): NavBarListLinkDTO {
        return new NavBarListLinkDTO(
            $displayName,
            $link,
        );
    }
}
