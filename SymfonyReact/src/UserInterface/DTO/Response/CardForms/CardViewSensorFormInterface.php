<?php

namespace App\UserInterface\DTO\Response\CardForms;

use App\UserInterface\DTO\Response\State\StateResponseDTO;
use App\UserInterface\DTO\Response\Colours\ColourResponseDTO;
use App\UserInterface\DTO\Response\Icons\IconResponseDTO;

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
