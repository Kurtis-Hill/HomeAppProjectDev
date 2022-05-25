<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardViewTypeFilterDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
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
        string $view = null
    ): array;
}
