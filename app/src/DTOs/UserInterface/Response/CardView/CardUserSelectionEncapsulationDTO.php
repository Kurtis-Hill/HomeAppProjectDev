<?php

namespace App\DTOs\UserInterface\Response\CardView;

use App\DTOs\UserInterface\Response\Colours\ColourResponseDTO;
use App\DTOs\UserInterface\Response\Icons\IconResponseDTO;
use App\DTOs\UserInterface\Response\State\StateResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class CardUserSelectionEncapsulationDTO
{
    #[ArrayShape([IconResponseDTO::class])]
    private array $icons;

    #[ArrayShape([ColourResponseDTO::class])]
    private array $colours;

    #[ArrayShape([StateResponseDTO::class])]
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

    #[ArrayShape([StateResponseDTO::class])]
    public function getStates(): array
    {
        return $this->states;
    }
}
