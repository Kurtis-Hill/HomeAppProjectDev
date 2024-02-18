<?php

namespace App\UserInterface\Services\Cards\CardViewUpdate;

use App\UserInterface\DTO\Internal\CardUpdateDTO\CardUpdateDTO;
use App\UserInterface\Entity\Card\CardView;
use JetBrains\PhpStorm\ArrayShape;

interface CardViewUpdateInterface
{
    #[ArrayShape(["errors"])]
    public function updateAllCardViewObjectProperties(CardUpdateDTO $cardUpdateDTO, CardView $cardView): array;
}
