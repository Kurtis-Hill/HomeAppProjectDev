<?php

namespace App\UserInterface\Builders\UsersCardSelectionBuilders;

use App\UserInterface\Builders\CardStateDTOBuilders\CardStateDTOBuilder;
use App\UserInterface\Builders\ColoursDTOBuilders\ColourDTOBuilder;
use App\UserInterface\Builders\IconDTOBuilder\IconDTOBuilder;
use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\DTO\Response\Colours\ColourResponseDTO;
use App\UserInterface\DTO\Response\Icons\IconResponseDTO;
use App\UserInterface\Repository\ORM\CardRepositories\CardColourRepositoryInterface;
use App\UserInterface\Repository\ORM\CardRepositories\CardStateRepositoryInterface;
use App\UserInterface\Repository\ORM\IconsRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;

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

    #[ArrayShape(
        [
            'icons' => [IconResponseDTO::class],
            'colours' => [ColourResponseDTO::class],
            'states' => [CardStateResponseDTO::class]
        ]
    )]
    public function buildUsersCardSelectionDTOs(): array
    {
        return [
            'icons' => $this->getIconSelectionAsDTOs(),
            'colours' => $this->getColourSelectionAsDTOs(),
            'states' => $this->getStateSelectionAsDTOs(),
        ];
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
