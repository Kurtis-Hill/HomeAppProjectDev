<?php

namespace App\UserInterface\DTO\Response\Colours;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class ColourResponseDTO
{
    private int $colourID;

    private string $colour;

    private string $shade;

    public function __construct(int $cardColourID, string $colour, string $shade)
    {
        $this->colourID = $cardColourID;
        $this->colour = $colour;
        $this->shade = $shade;
    }

    public function getColourID(): int
    {
        return $this->colourID;
    }

    public function getColour(): string
    {
        return $this->colour;
    }

    public function getShade(): string
    {
        return $this->shade;
    }

}
