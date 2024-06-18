<?php

namespace App\Builders\UserInterface\UsersCardSelectionBuilders;

use App\Builders\UserInterface\CardStateDTOBuilders\CardStateDTOBuilder;
use App\Builders\UserInterface\ColoursDTOBuilders\ColourDTOBuilder;
use App\Builders\UserInterface\IconDTOBuilder\IconDTOBuilder;
use App\DTOs\UserInterface\Response\CardView\CardUserSelectionEncapsulationDTO;
use App\Repository\UserInterface\ORM\CardRepositories\CardColourRepositoryInterface;
use App\Repository\UserInterface\ORM\CardRepositories\CardStateRepositoryInterface;
use App\Repository\UserInterface\ORM\IconsRepositoryInterface;

class UsersCardSelectionBuilder
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


    public function buildUsersCardSelectionDTOs(): CardUserSelectionEncapsulationDTO
    {
        return new CardUserSelectionEncapsulationDTO(
            $this->getIconSelectionAsDTOs(),
            $this->getColourSelectionAsDTOs(),
            $this->getStateSelectionAsDTOs(),
        );
    }

    private function getIconSelectionAsDTOs(): array
    {
        $iconObjects = $this->iconsRepository->getAllIconObjects();

        foreach ($iconObjects as $iconObject) {
            $iconDTOs[] = IconDTOBuilder::buildIconResponseDTO($iconObject);
        }

        return $iconDTOs ?? [];
    }

    private function getColourSelectionAsDTOs(): array
    {
        $cardColourObjects = $this->cardColourRepository->getAllColourObjects();

        foreach ($cardColourObjects as $cardColourObject) {
            $cardColourDTOs[] = ColourDTOBuilder::buildColourResponseDTO($cardColourObject);
        }

        return $cardColourDTOs ?? [];
    }

    private function getStateSelectionAsDTOs(): array
    {
        $cardStateObjects = $this->cardStateRepository->getAllStateAsObjects();

        foreach ($cardStateObjects as $cardStateObject) {
            $cardStateDTOs[] = CardStateDTOBuilder::buildCardStateResponseDTO($cardStateObject);
        }

        return $cardStateDTOs ?? [];
    }
}
