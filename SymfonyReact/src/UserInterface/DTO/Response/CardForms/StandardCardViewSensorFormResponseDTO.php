<?php

namespace App\UserInterface\DTO\Response\CardForms;

use App\UserInterface\DTO\RequestDTO\CardUserSelectionEncapsulationDTO;
use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\UserInterface\DTO\Response\Colours\ColourResponseDTO;
use App\UserInterface\DTO\Response\Icons\IconResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class StandardCardViewSensorFormResponseDTO implements CardViewSensorFormInterface
{
    private int $sensorID;

    private IconResponseDTO $currentCardIcon;

    private ColourResponseDTO $currentCardColour;

    private CardStateResponseDTO $currentViewState;

    private CardUserSelectionEncapsulationDTO $cardUserSelectionOptions;

    private string $cardViewID;

    #[ArrayShape([StandardCardViewReadingResponseDTO::class])]
    private array $sensorData;

    public function __construct(
        int $sensorId,
        IconResponseDTO $currentCardIcon,
        ColourResponseDTO $currentCardColour,
        CardStateResponseDTO $currentViewState,
        string $cardViewID,
        CardUserSelectionEncapsulationDTO $cardUserSelectionOptions,
        array $sensorData,
    ) {
        $this->sensorID = $sensorId;
        $this->currentCardIcon = $currentCardIcon;
        $this->currentCardColour = $currentCardColour;
        $this->currentViewState = $currentViewState;
        $this->cardViewID = $cardViewID;
        $this->cardUserSelectionOptions = $cardUserSelectionOptions;
        $this->sensorData = $sensorData;
    }

    public function getSensorID(): int
    {
        return $this->sensorID;
    }

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

    public function getCurrentViewState(): CardStateResponseDTO
    {
        return $this->currentViewState;
    }

    public function getCardUserSelectionOptions(): CardUserSelectionEncapsulationDTO
    {
        return $this->cardUserSelectionOptions;
    }
}
