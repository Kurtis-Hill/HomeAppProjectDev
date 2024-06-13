<?php

namespace App\DTOs\UserInterface\Response\CardForms;

use App\DTOs\UserInterface\Response\Colours\ColourResponseDTO;
use App\DTOs\UserInterface\Response\Icons\IconResponseDTO;
use App\DTOs\UserInterface\Response\State\StateResponseDTO;

interface CardViewSensorFormInterface
{
    public function getSensorData(): array;

    public function getCardViewID(): int;

    public function getCurrentCardIcon(): IconResponseDTO;

    public function getCurrentCardColour(): ColourResponseDTO;

    public function getCurrentViewState(): StateResponseDTO;

//    public function getIconSelection(): array;
//
//    public function getUserColourSelections(): array;
//
//    public function getUserCardViewSelections(): array;
}
