<?php

namespace App\UserInterface\DTO\Response\CardView;

use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\DTO\Response\Colours\ColourResponseDTO;
use App\UserInterface\DTO\Response\Icons\IconResponseDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardViewResponseDTO
{
    private int $cardViewID;

    private IconResponseDTO $cardIcon;

    private ColourResponseDTO $cardColour;

    private CardStateResponseDTO $cardViewState;

    public function __construct(
        int $cardViewID,
        IconResponseDTO $cardIcon,
        ColourResponseDTO $cardColour,
        CardStateResponseDTO $cardStateDTO
    ) {
        $this->cardViewID = $cardViewID;
        $this->cardIcon = $cardIcon;
        $this->cardColour = $cardColour;
        $this->cardViewState = $cardStateDTO;
    }

    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

    public function getCardIcon(): IconResponseDTO
    {
        return $this->cardIcon;
    }


    public function getCardColour(): ColourResponseDTO
    {
        return $this->cardColour;
    }

    public function getCardViewState(): CardStateResponseDTO
    {
        return $this->cardViewState;
    }
}
