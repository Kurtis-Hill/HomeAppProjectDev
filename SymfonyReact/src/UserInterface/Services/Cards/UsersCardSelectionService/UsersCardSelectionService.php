<?php

namespace App\UserInterface\Services\Cards\UsersCardSelectionService;

use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Icons;
use App\UserInterface\Repository\ORM\CardRepositories\CardColourRepositoryInterface;
use App\UserInterface\Repository\ORM\CardRepositories\CardStateRepositoryInterface;
use App\UserInterface\Repository\ORM\IconsRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

class UsersCardSelectionService implements UsersCardSelectionServiceInterface
{
    private IconsRepositoryInterface $iconsRepository;

    private CardColourRepositoryInterface $cardColourRepository;

    private CardStateRepositoryInterface $cardStateRepository;

    public function __construct(
        IconsRepositoryInterface $iconsRepository,
        CardColourRepositoryInterface $cardColourRepository,
        CardStateRepositoryInterface $cardStateRepository,
    ) {
        $this->iconsRepository = $iconsRepository;
        $this->cardColourRepository = $cardColourRepository;
        $this->cardStateRepository = $cardStateRepository;
    }

    /**
     * @throws ORMException
     */
    #[ArrayShape(['icons' => [Icons::class], 'colours' => [CardColour::class], 'states' => [Cardstate::class]])]
    public function getUsersStandardCardSelections(): array
    {
        return [
            'icons' => $this->getIconSelection(),
            'colours' => $this->getColourSelection(),
            'states' => $this->getStateSelection(),
        ];
    }

    /**
     * @throws ORMException
     */
    private function getIconSelection(): array
    {
        return $this->iconsRepository->getAllIcons();
    }

    /**
     * @throws ORMException
     */
    private function getColourSelection(): array
    {
        return $this->cardColourRepository->getAllColours();
    }

    /**
     * @throws ORMException
     */
    private function getStateSelection(): array
    {
        return $this->cardStateRepository->getAllStates();
    }
}
