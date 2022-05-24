<?php

namespace App\UserInterface\DTO\Response\CardForms;

use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\DTO\Response\CardViewDTO\StandardCardViewDTO;
use App\UserInterface\DTO\Response\Colours\CardColourResponseDTO;
use App\UserInterface\DTO\Response\Icons\IconResponseDTO;
use JetBrains\PhpStorm\ArrayShape;

class StandardCardViewSensorFormDTO implements CardViewSensorFormInterface
{
    private int $sensorId;

    private IconResponseDTO $currentCardIcon;

    private CardColourResponseDTO $currentCardColour;

    private CardStateResponseDTO $currentState;

    private string $cardViewID;

    #[ArrayShape([IconResponseDTO::class])]
    private array $iconSelection;

    #[ArrayShape([CardColourResponseDTO::class])]
    private array $colourSelection;

    #[ArrayShape([IconResponseDTO::class])]
    private array $cardStates;

    #[ArrayShape([StandardCardViewDTO::class])]
    private array $sensorData;

    public function __construct(
        int $sensorId,
        IconResponseDTO $currentCardIcon,
        CardColourResponseDTO $currentCardColour,
        CardStateResponseDTO $currentState,
        string $cardViewID,
        array $iconSelection,
        array $colourSelection,
        array $cardStates,
        array $sensorData,
    ) {
        $this->sensorId = $sensorId;
        $this->currentCardIcon = $currentCardIcon;
        $this->currentCardColour = $currentCardColour;
        $this->currentState = $currentState;
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

    public function getCurrentCardColour(): CardColourResponseDTO
    {
        return $this->currentCardColour;
    }

    public function getCurrentState(): CardStateResponseDTO
    {
        return $this->currentState;
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
