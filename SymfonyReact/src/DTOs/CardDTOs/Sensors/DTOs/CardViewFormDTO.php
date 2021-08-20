<?php

namespace App\DTOs\CardDTOs\Sensors\DTOs;

interface CardViewFormDTO
{
    public function getCurrentViewState(): array;

    public function getCurrentCardIcon(): array;

    public function getCurrentCardColour(): array;
}
