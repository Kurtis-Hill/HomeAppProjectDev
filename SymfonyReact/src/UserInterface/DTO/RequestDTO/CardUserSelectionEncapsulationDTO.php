<?php

namespace App\UserInterface\DTO\RequestDTO;

use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\DTO\Response\Colours\ColourResponseDTO;
use App\UserInterface\DTO\Response\Icons\IconResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardUserSelectionEncapsulationDTO
{
    #[ArrayShape([IconResponseDTO::class])]
    private array $icons;

    #[ArrayShape([ColourResponseDTO::class])]
    private array $colours;

    #[ArrayShape([CardStateResponseDTO::class])]
    private array $states;

    public function __construct(
        array $icons,
        array $colours,
        array $states,
    ) {
        $this->icons = $icons;
        $this->colours = $colours;
        $this->states = $states;
    }

    #[ArrayShape([IconResponseDTO::class])]
    public function getIcons(): array
    {
        return $this->icons;
    }

    #[ArrayShape([ColourResponseDTO::class])]
    public function getColours(): array
    {
        return $this->colours;
    }

    #[ArrayShape([CardStateResponseDTO::class])]
    public function getStates(): array
    {
        return $this->states;
    }
}