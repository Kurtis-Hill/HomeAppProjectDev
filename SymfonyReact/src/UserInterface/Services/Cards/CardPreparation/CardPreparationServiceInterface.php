<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\UserInterface\DTO\CardDataFiltersDTO\CardDataPostFilterDTO;
use App\UserInterface\Exceptions\WrongUserTypeException;
use Symfony\Component\Security\Core\User\UserInterface;

interface CardPreparationServiceInterface
{
    /**
     * @throws WrongUserTypeException
     */
    public function prepareCardsForUser(UserInterface $user, CardDataPostFilterDTO $cardDataPostFilterDTO, string $view): array;
}
