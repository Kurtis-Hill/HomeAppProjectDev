<?php

namespace App\UserInterface\Builders\NewCardOptionsDTOBuilder;

use App\UserInterface\DTO\Internal\NewCard\NewCardOptionsDTO;

class NewCardOptionsBuilder
{
    public static function buildNewCardOptionsDTO(
        ?int $iconID,
        ?int $colourID,
        ?int $stateID,
    ): NewCardOptionsDTO {
        return new NewCardOptionsDTO(
            $iconID,
            $colourID,
            $stateID,
        );
    }
}
