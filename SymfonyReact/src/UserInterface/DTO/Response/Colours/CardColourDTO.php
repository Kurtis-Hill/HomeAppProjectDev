<?php

namespace App\UserInterface\DTO\Response\Colours;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardColourDTO
{
    private int $cardColourID;

    private string $colour;

    private string $shade;

    public function __construct(int $cardColourID, string $colour, string $shade)
    {
        $this->cardColourID = $cardColourID;
        $this->colour = $colour;
        $this->shade = $shade;
    }

    public function getCardColourID(): int
    {
        return $this->cardColourID;
    }

    public function getColour(): string
    {
        return $this->colour;
    }

    /**
     * @return string
     */
    public function getShade(): string
    {
        return $this->shade;
    }

}
