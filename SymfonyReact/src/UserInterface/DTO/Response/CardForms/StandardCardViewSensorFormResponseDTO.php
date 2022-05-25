<?php

namespace App\UserInterface\DTO\Response\CardForms;

use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\DTO\Response\CardViewReadingDTO\StandardCardViewReadingResponseDTO;
use App\UserInterface\DTO\Response\Colours\ColourResponseDTO;
use App\UserInterface\DTO\Response\Icons\IconResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class StandardCardViewSensorFormResponseDTO implements CardViewSensorFormInterface
{
    private int $sensorId;

    private IconResponseDTO $currentCardIcon;

    private ColourResponseDTO $currentCardColour;

    private CardStateResponseDTO $currentViewState;

    private string $cardViewID;

    #[ArrayShape([IconResponseDTO::class])]
    private array $iconSelection;

    #[ArrayShape([ColourResponseDTO::class])]
    private array $colourSelection;

    #[ArrayShape([IconResponseDTO::class])]
    private array $cardStates;

    #[ArrayShape([StandardCardViewReadingResponseDTO::class])]
    private array $sensorData;

    public function __construct(
        int $sensorId,
        IconResponseDTO $currentCardIcon,
        ColourResponseDTO $currentCardColour,
        CardStateResponseDTO $currentViewState,
        string $cardViewID,
        array $iconSelection,
        array $colourSelection,
        array $cardStates,
        array $sensorData,
    ) {
        $this->sensorId = $sensorId;
        $this->currentCardIcon = $currentCardIcon;
        $this->currentCardColour = $currentCardColour;
        $this->currentViewState = $currentViewState;
        $this->cardViewID = $cardViewID;
        $this->iconSelection = $iconSelection;
        $this->colourSelection = $colourSelection;
        $this->cardStates = $cardStates;
        $this->sensorData = $sensorData;
    }

    public function getSensorId(): int
    {
        return $this->sensorId;
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

    public function getIconSelection(): array
    {
        return $this->iconSelection;
    }

    public function getUserColourSelections(): array
    {
        return $this->colourSelection;
    }

    public function getUserCardViewSelections(): array
    {
        return $this->cardStates;
    }
}
