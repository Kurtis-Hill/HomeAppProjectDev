<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\User\Entity\User;
use App\UserInterface\Controller\Card\GetCardViewController;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardViewUriFilterDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\Entity\Card\CardState;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;

class CurrentReadingCardViewPreparationHandler
{
    private CardViewRepositoryInterface $cardViewRepository;

    public function __construct(CardViewRepositoryInterface $cardViewRepository,)
    {
        $this->cardViewRepository = $cardViewRepository;
    }

    /**
     * @throws ORMException
     */
    public function prepareCardsForUser(
        User $user,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        CardViewUriFilterDTO $cardViewTypeFilterDTO,
        string $view = null,
    ): array {
        $cardViewTwo = match ($view) {
            GetCardViewController::ROOM_VIEW => CardState::ROOM_ONLY,
            GetCardViewController::DEVICE_VIEW => CardState::DEVICE_ONLY,
            default => CardState::INDEX_ONLY
        };

        return $this->cardViewRepository->getAllCardSensorDataForUser(
            $user,
            $cardViewTwo,
            $cardDataPostFilterDTO,
            $cardViewTypeFilterDTO
        );
    }
}
