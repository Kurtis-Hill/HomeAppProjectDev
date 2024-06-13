<?php

namespace App\Services\UserInterface\Cards\CardViewUpdate;

use App\DTOs\UserInterface\Internal\CardUpdateDTO\CardUpdateDTO;
use App\Entity\UserInterface\Card\CardView;
use JetBrains\PhpStorm\ArrayShape;

interface CardViewUpdateInterface
{
    #[ArrayShape(["errors"])]
    public function updateAllCardViewObjectProperties(CardUpdateDTO $cardUpdateDTO, CardView $cardView): array;
}
