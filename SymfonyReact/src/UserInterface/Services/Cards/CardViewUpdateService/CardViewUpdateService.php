<?php

namespace App\UserInterface\Services\Cards\CardViewUpdateService;

use App\Common\Traits\ValidatorProcessorTrait;
use App\UserInterface\DTO\CardUpdateDTO\StandardCardUpdateDTO;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Repository\ORM\CardRepositories\CardColourRepositoryInterface;
use App\UserInterface\Repository\ORM\CardRepositories\CardStateRepositoryInterface;
use App\UserInterface\Repository\ORM\IconsRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CardViewUpdateService implements CardViewUpdateServiceInterface
{
    use ValidatorProcessorTrait;

    private ValidatorInterface $validator;

    private CardColourRepositoryInterface $cardColourRepository;

    private IconsRepositoryInterface $iconsRepository;

    private CardStateRepositoryInterface $cardStateRepository;

    public function __construct(
        ValidatorInterface $validator,
        CardColourRepositoryInterface $cardColourRepository,
        IconsRepositoryInterface $iconsRepository,
        CardStateRepositoryInterface $cardStateRepository,
    ) {
        $this->validator = $validator;
        $this->cardColourRepository = $cardColourRepository;
        $this->iconsRepository = $iconsRepository;
        $this->cardStateRepository = $cardStateRepository;
    }

    public function handleStandardCardUpdateRequest(StandardCardUpdateDTO $cardUpdateDTO, CardView $cardView): array
    {
        $cardColour = $this->cardColourRepository->findOneById($cardUpdateDTO->getCardColourID());
        $cardIcon = $this->iconsRepository->findOneById($cardUpdateDTO->getCardIconID());
        $cardState = $this->cardStateRepository->findOneById($cardUpdateDTO->getCardStateID());

        $cardView->setCardColourID($cardColour);
        $cardView->setCardIconID($cardIcon);
        $cardView->setCardStateID($cardState);

        return $this->validateCardViewObject($cardView);
    }

    private function validateCardViewObject(CardView $cardView): array
    {
        $constraintViolationList = $this->validator->validate($cardView);

        if ($this->checkIfErrorsArePresent($constraintViolationList)) {
            return $this->returnValidationErrorAsArray($constraintViolationList);
        }

        return [];
    }

}
