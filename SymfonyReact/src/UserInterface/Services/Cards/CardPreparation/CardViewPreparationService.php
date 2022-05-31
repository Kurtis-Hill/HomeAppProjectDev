<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\User\Entity\User;
use App\UserInterface\Controller\Card\CardController;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardViewTypeFilterDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Exceptions\WrongUserTypeException;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Security\Core\User\UserInterface;

class CardViewPreparationService implements CardViewPreparationServiceInterface
{
    private CardViewRepositoryInterface $cardViewRepository;

    public function __construct(CardViewRepositoryInterface $cardViewRepository,)
    {
        $this->cardViewRepository = $cardViewRepository;
    }

    /**
     * @throws WrongUserTypeException
     * @throws ORMException
     */
    public function prepareCardsForUser(
        UserInterface $user,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        CardViewTypeFilterDTO $cardViewTypeFilterDTO,
        string $view = null,
    ): array
    {
        if (!$user instanceof User) {
            throw new WrongUserTypeException();
        }
        return match ($view) {
            CardController::ROOM_VIEW => $this->getRoomCardDataObjects($user, $cardDataPostFilterDTO, $cardViewTypeFilterDTO),
            CardController::DEVICE_VIEW => $this->getDevicePageCardDataObjects($user, $cardDataPostFilterDTO, $cardViewTypeFilterDTO),
            default => $this->getIndexPageCardDataObjects($user, $cardDataPostFilterDTO)
        };
    }

    /**
     * @throws ORMException
     */
    private function getIndexPageCardDataObjects(User $user, CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO): array
    {
        return $this->cardViewRepository->getAllCardSensorData(
            $user,
            Cardstate::INDEX_ONLY,
            $cardDataPostFilterDTO,
        );
    }

    /**
     * @throws ORMException
     */
    private function getDevicePageCardDataObjects(
        User $user,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        CardViewTypeFilterDTO $cardViewTypeFilterDTO
    ): array
    {
        return $this->cardViewRepository->getAllCardSensorData(
            $user,
            Cardstate::DEVICE_ONLY,
            $cardDataPostFilterDTO,
            $cardViewTypeFilterDTO,
        );

    }

    /**
     * @throws ORMException
     */
    private function getRoomCardDataObjects(
        User $user,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        CardViewTypeFilterDTO $cardViewTypeFilterDTO
    ): array
    {
        return $this->cardViewRepository->getAllCardSensorData(
            $user,
            Cardstate::ROOM_ONLY,
            $cardDataPostFilterDTO,
            $cardViewTypeFilterDTO
        );
    }
}
