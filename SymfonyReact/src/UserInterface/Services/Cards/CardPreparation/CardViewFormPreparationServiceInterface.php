<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\DTOs\CardDTOs\Sensors\DTOs\CardViewSensorFormDTO;
use App\UserInterface\Entity\Card\CardView;

interface CardViewFormPreparationServiceInterface
{
    public function getCardViewFormDTO(CardView $cardViewObject): ?CardViewSensorFormDTO;
}
