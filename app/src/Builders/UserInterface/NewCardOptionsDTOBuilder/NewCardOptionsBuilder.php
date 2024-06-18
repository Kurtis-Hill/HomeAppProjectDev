<?php

namespace App\Builders\UserInterface\NewCardOptionsDTOBuilder;

use App\DTOs\UserInterface\Internal\NewCard\NewCardOptionsDTO;

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
