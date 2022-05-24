<?php

namespace App\UserInterface\Services\Cards\CardViewUpdateService;

use App\UserInterface\DTO\Internal\CardUpdateDTO\CardUpdateDTO;
use App\UserInterface\Entity\Card\CardView;
use JetBrains\PhpStorm\ArrayShape;

interface CardViewUpdateServiceInterface
{
    #[ArrayShape(["errors"])]
    public function updateAllCardViewObjectProperties(CardUpdateDTO $cardUpdateDTO, CardView $cardView): array;
}
