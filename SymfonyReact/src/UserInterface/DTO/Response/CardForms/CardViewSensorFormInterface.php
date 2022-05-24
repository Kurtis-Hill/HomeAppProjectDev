<?php

namespace App\UserInterface\DTO\Response\CardForms;

use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\DTO\Response\Colours\CardColourResponseDTO;
use App\UserInterface\DTO\Response\Icons\IconResponseDTO;

interface CardViewSensorFormInterface
{
    public function getSensorData(): array;

    public function getCardViewID(): int;

    public function getCurrentCardIcon(): IconResponseDTO;

    public function getCurrentCardColour(): CardColourResponseDTO;

    public function getCurrentState(): CardStateResponseDTO;

    public function getIconSelection(): array;

    public function getUserColourSelections(): array;

    public function getUserCardViewSelections(): array;
}
