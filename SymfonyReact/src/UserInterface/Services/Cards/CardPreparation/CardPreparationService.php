<?php

namespace App\UserInterface\Services\Cards\CardPreparation;

use App\User\Entity\User;
use App\UserInterface\DTO\CardDataFiltersDTO\CardDataPostFilterDTO;
use App\UserInterface\Exceptions\WrongUserTypeException;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CardPreparationService implements CardPreparationServiceInterface
{
    private CardViewRepositoryInterface $cardViewRepository;

    public function __construct(CardViewRepositoryInterface $cardViewRepository)
    {
        $this->cardViewRepository = $cardViewRepository;
    }

    public function prepareCardsForUser(UserInterface $user, CardDataPostFilterDTO $cardDataPostFilterDTO, string $view): array
    {
        if (!$user instanceof User) {
            throw new WrongUserTypeException();
        }

        $sensorObjects = match ($view) {
            "room" => $this->getRoomCardDataObjects($user, $cardDataPostFilterDTO),
            "device" => $this->getDevicePageCardDataObjects($user, $cardDataPostFilterDTO),
            default => $this->getIndexPageCardDataObjects($user, $cardDataPostFilterDTO)
        };
    }


    private function getIndexPageCardDataObjects(User $user, CardDataPostFilterDTO $cardDataPostFilterDTO): array
    {
        $filteredData = [
            'sensorTypes' => $cardDataPostFilterDTO->getSensorTypesToQuery(),
            'readingTypes' => $cardDataPostFilterDTO->getReadingTypesToQuery(),
        ];

        $sensorData = $this->cardViewRepository->getAllIndexCardDataForUser($user, $filteredData);

    }

    private function getRoomCardDataObjects(User $user, CardDataPostFilterDTO $cardDataPostFilterDTO): array
    {

    }
    private function getDevicePageCardDataObjects(User $user, CardDataPostFilterDTO $cardDataPostFilterDTO): array
    {

    }
}
