<?php

namespace App\DTOs\CardDTOs\Sensors\DTOs;

interface CardDTOInterface
{
    public function getSensorData(): array;

    public function getSensorType(): string;

    public function getSensorRoom(): string;

    public function getCardIcon(): string;

    public function getCardColour(): string;

    public function getCardViewID(): int;
}
