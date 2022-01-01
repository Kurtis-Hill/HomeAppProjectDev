<?php

namespace App\UserInterface\DTO\CardUpdateDTO;

class StandardCardUpdateDTO
{
    private int $cardColourID;

    private int $cardIconID;

    private int $cardStateID;

    public function __construct(
        ?int $cardColourID,
        ?int $cardIconID,
        ?int $cardStateID,
    ) {
        $this->cardColourID = $cardColourID;
        $this->cardIconID = $cardIconID;
        $this->cardStateID = $cardStateID;
    }

    public function getCardColourID(): ?int
    {
        return $this->cardColourID;
    }

    public function getCardIconID(): ?int
    {
        return $this->cardIconID;
    }

    public function getCardStateID(): ?int
    {
        return $this->cardStateID;
    }
}
