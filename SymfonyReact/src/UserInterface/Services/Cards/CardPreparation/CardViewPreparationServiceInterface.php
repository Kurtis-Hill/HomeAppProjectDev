<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\UserInterface\DTO\CardDataFiltersDTO\CardViewTypeFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\Exceptions\WrongUserTypeException;
use Symfony\Component\Security\Core\User\UserInterface;

interface CardViewPreparationServiceInterface
{
    /**
     * @throws WrongUserTypeException
     */
    public function prepareCardsForUser(
        UserInterface $user,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        CardViewTypeFilterDTO $cardViewTypeFilterDTO,
        string $view
    ): array;
}
