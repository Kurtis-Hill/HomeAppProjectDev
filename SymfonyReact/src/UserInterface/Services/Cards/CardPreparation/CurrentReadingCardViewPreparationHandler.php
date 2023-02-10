<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\User\Entity\User;
use App\UserInterface\Controller\Card\CardViewController;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardViewUriFilterDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\Entity\Card\Cardstate;
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
            CardViewController::ROOM_VIEW => Cardstate::ROOM_ONLY,
            CardViewController::DEVICE_VIEW => Cardstate::DEVICE_ONLY,
            default => Cardstate::INDEX_ONLY
        };

        return $this->cardViewRepository->getAllCardSensorDataForUser(
            $user,
            $cardViewTwo,
            $cardDataPostFilterDTO,
            $cardViewTypeFilterDTO
        );
    }
}
