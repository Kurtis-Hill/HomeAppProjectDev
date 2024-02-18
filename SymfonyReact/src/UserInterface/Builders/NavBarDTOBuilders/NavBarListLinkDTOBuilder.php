<?php

namespace App\UserInterface\Builders\NavBarDTOBuilders;

use App\UserInterface\DTO\Response\NavBar\NavBarListLinkDTO;

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
