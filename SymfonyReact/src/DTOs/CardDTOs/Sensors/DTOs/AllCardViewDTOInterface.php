<?php

namespace App\DTOs\CardDTOs\Sensors\DTOs;

interface AllCardViewDTOInterface
{
    public function getCardViewID(): int;

    public function getSensorData(): array;
}
