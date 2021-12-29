<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\User\Entity\User;
use App\UserInterface\Controller\Card\CardController;
use App\UserInterface\DTO\CardDataFiltersDTO\CardViewTypeFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\Exceptions\WrongUserTypeException;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CardViewPreparationService implements CardViewPreparationServiceInterface
{
    private CardViewRepositoryInterface $cardViewRepository;

    public function __construct(
        CardViewRepositoryInterface $cardViewRepository,
    ) {
        $this->cardViewRepository = $cardViewRepository;
    }

    public function prepareCardsForUser(
        UserInterface $user,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        CardViewTypeFilterDTO $cardViewTypeFilterDTO,
        string $view
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

    private function getDevicePageCardDataObjects(User $user, CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO, CardViewTypeFilterDTO $cardViewTypeFilterDTO): array
    {
        $cardSensorData = $this->cardViewRepository->getAllCardSensorDataScalar($user, $cardDataPostFilterDTO, $cardViewTypeFilterDTO);

        return $cardSensorData;

    }

    private function getIndexPageCardDataObjects(User $user, CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO): array
    {
        $cardSensorData = $this->cardViewRepository->getAllCardSensorDataScalar($user, $cardDataPostFilterDTO);

        return $cardSensorData;
    }

    private function getRoomCardDataObjects(User $user, CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO, CardViewTypeFilterDTO $cardViewTypeFilterDTO): array
    {
        $cardSensorData = $this->cardViewRepository->getAllCardSensorDataScalar($user, $cardDataPostFilterDTO);

        return [];
    }
}
