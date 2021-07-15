<?php

namespace App\DTOs\Sensors\CardDTOs;

interface AllCardViewDTOInterface
{
    public function getCardViewID(): int;

    public function getSensorData(): array;
}
