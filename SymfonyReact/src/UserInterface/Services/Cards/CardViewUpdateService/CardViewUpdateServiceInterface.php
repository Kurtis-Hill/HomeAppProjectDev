<?php

namespace App\UserInterface\Services\Cards\CardViewUpdateService;

use App\UserInterface\DTO\CardUpdateDTO\StandardCardUpdateDTO;
use App\UserInterface\Entity\Card\CardView;

interface CardViewUpdateServiceInterface
{
    public function handleStandardCardUpdateRequest(StandardCardUpdateDTO $cardUpdateDTO, CardView $cardView): array;
}
