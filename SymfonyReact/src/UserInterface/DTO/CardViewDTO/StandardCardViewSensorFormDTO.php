<?php

namespace App\UserInterface\DTO\CardViewDTO;

class StandardCardViewSensorFormDTO implements CardViewSensorFormInterface
{
    private array $sensorData;

    private array $currentCardIcon;

    private array $currentCardColour;

    private array $currentState;

    private string $cardViewID;

    private array $iconSelection;

    private array $colourSelection;

    private array $cardStates;

    public function __construct(
        array $currentCardIcon,
        array $currentCardColour,
        array $currentState,
        string $cardViewID,
        array $iconSelection,
        array $colourSelection,
        array $cardStates,
        array $sensorData,
    ) {
        $this->currentCardIcon = $currentCardIcon;
        $this->currentCardColour = $currentCardColour;
        $this->currentState = $currentState;
        $this->cardViewID = $cardViewID;
        $this->iconSelection = $iconSelection;
        $this->colourSelection = $colourSelection;
        $this->cardStates = $cardStates;
        $this->sensorData = $sensorData;
    }

    public function getSensorData(): array
    {
        return $this->sensorData;
    }

    public function getCardIcon(): array
    {
        return $this->currentCardIcon;
    }

    public function getCardColour(): array
    {
        return $this->currentCardColour;
    }

    public function getCurrentViewState(): array
    {
        return $this->currentState;
    }

    public function getCardViewID(): int
    {
        return $this->cardViewID;
    }

    public function getCurrentCardIcon(): array
    {
        return $this->currentCardIcon;
    }

    public function getCurrentCardColour(): array
    {
        return $this->currentCardColour;
    }

    public function getCurrentState(): array
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
