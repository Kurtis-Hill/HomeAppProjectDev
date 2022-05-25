<?php

namespace App\UserInterface\Services\Cards\UsersCardSelectionService;

use App\UserInterface\Builders\CardStateDTOBuilders\CardStateDTOBuilder;
use App\UserInterface\Builders\ColoursDTOBuilders\ColourDTOBuilder;
use App\UserInterface\Builders\IconDTOBuilder\IconDTOBuilder;
use App\UserInterface\DTO\Response\CardState\CardStateResponseDTO;
use App\UserInterface\DTO\Response\Colours\ColourResponseDTO;
use App\UserInterface\DTO\Response\Icons\IconResponseDTO;
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
    #[ArrayShape(
        [
            'icons' => [Icons::class],
            'colours' => [CardColour::class],
            'states' => [Cardstate::class]
        ]
    )]
    public function getUsersStandardCardSelectionsAsArray(): array
    {
        return [
            'icons' => $this->getIconSelectionAsArray(),
            'colours' => $this->getColourSelectionAsArray(),
            'states' => $this->getStateSelectionAsArray(),
        ];
    }

    /**
     * @throws ORMException
     */
    #[ArrayShape(
        [
            'iconID' => "int",
            'iconName' => "string",
            'description' => "string"
        ]
    )]
    private function getIconSelectionAsArray(): array
    {
        return $this->iconsRepository->getAllIconsAsArray();
    }

    /**
     * @throws ORMException
     */
    #[ArrayShape(['colourID' => "int", 'colour' => "string", 'shade' => "string"])]
    private function getColourSelectionAsArray(): array
    {
        return $this->cardColourRepository->getAllColoursAsArray();
    }

    /**
     * @throws ORMException
     */
    #[ArrayShape(['cardStateID' => "int", 'state' => "string"])]
    private function getStateSelectionAsArray(): array
    {
        return $this->cardStateRepository->getAllStatesAsArray();
    }

    #[ArrayShape(
        [
            'icons' => [IconResponseDTO::class],
            'colours' => [ColourResponseDTO::class],
            'states' => [CardStateResponseDTO::class]
        ]
    )]
    public function getUsersCardSelectionAsDTOs(): array
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
