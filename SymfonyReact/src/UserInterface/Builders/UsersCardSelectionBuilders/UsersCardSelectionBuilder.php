<?php

namespace App\UserInterface\Builders\UsersCardSelectionBuilders;

use App\UserInterface\Builders\CardStateDTOBuilders\CardStateDTOBuilder;
use App\UserInterface\Builders\ColoursDTOBuilders\ColourDTOBuilder;
use App\UserInterface\Builders\IconDTOBuilder\IconDTOBuilder;
use App\UserInterface\DTO\Response\CardView\CardUserSelectionEncapsulationDTO;
use App\UserInterface\Repository\ORM\CardRepositories\CardColourRepositoryInterface;
use App\UserInterface\Repository\ORM\CardRepositories\CardStateRepositoryInterface;
use App\UserInterface\Repository\ORM\IconsRepositoryInterface;

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
