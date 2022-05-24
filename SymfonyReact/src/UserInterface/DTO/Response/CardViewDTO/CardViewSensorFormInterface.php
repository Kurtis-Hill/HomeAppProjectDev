<?php

namespace App\UserInterface\DTO\Response\CardViewDTO;

interface CardViewSensorFormInterface
{
    public function getSensorData(): array;

    public function getCardIcon(): array;

    public function getCardColour(): array;

    public function getCurrentViewState(): array;

    public function getCardViewID(): int;

    public function getCurrentCardIcon(): array;

    public function getCurrentCardColour(): array;

    public function getCurrentState(): array;

    public function getIconSelection(): array;

    public function getUserColourSelections(): array;

    public function getUserCardViewSelections(): array;
}
