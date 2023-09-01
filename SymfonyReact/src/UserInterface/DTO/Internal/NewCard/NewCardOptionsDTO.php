<?php

namespace App\UserInterface\DTO\Internal\NewCard;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class NewCardOptionsDTO
{
    public function __construct(
        private ?int $iconID = null,
        private ?int $colourID = null,
        private ?int $stateID = null,
    ) {
    }

    public function getIconID(): ?int
    {
        return $this->iconID;
    }

    public function getColourID(): ?int
    {
        return $this->colourID;
    }

    public function getStateID(): ?int
    {
        return $this->stateID;
    }
}
