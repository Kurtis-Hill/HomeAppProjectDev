<?php

namespace App\UserInterface\Services\Cards\CardViewUpdateService;

use App\UserInterface\DTO\CardUpdateDTO\StandardCardUpdateDTO;
use App\UserInterface\Entity\Card\CardView;
use JetBrains\PhpStorm\ArrayShape;

interface CardViewUpdateServiceInterface
{
    #[ArrayShape(["string"])]
    public function updateAllCardViewObjectProperties(StandardCardUpdateDTO $cardUpdateDTO, CardView $cardView): array;
}
