<?php

namespace App\DTOs\UserInterface\Response\CardForms;

use App\DTOs\UserInterface\Response\CardView\CardUserSelectionEncapsulationDTO;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\BoolCardViewReadingResponseDTO;
use App\DTOs\UserInterface\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\DTOs\UserInterface\Response\Colours\ColourResponseDTO;
use App\DTOs\UserInterface\Response\Icons\IconResponseDTO;
use App\DTOs\UserInterface\Response\State\StateResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class StandardCardViewSensorFormResponseDTO implements CardViewSensorFormInterface
{
    public function __construct(
        private int $sensorID,
        private IconResponseDTO $currentCardIcon,
        private ColourResponseDTO $currentCardColour,
        private StateResponseDTO $currentViewState,
        private int $cardViewID,
        private CardUserSelectionEncapsulationDTO $cardUserSelectionOptions,
        #[ArrayShape([StandardCardViewReadingResponseDTO::class|BoolCardViewReadingResponseDTO::class])]
        private array $sensorData,
    ) {
    }

    public function getSensorID(): int
    {
        return $this->sensorID;
    }

    #[ArrayShape([StandardCardViewReadingResponseDTO::class|BoolCardViewReadingResponseDTO::class])]
    public function getSensorData(): array
    {
        return $this->sensorData;
    }

    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

    public function getCurrentCardIcon(): IconResponseDTO
    {
        return $this->currentCardIcon;
    }

    public function getCurrentCardColour(): ColourResponseDTO
    {
        return $this->currentCardColour;
    }

    public function getCurrentViewState(): StateResponseDTO
    {
        return $this->currentViewState;
    }

    public function getCardUserSelectionOptions(): CardUserSelectionEncapsulationDTO
    {
        return $this->cardUserSelectionOptions;
    }
}
