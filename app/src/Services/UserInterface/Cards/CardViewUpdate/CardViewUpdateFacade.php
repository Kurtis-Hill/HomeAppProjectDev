<?php

namespace App\Services\UserInterface\Cards\CardViewUpdate;

use App\DTOs\UserInterface\Internal\CardUpdateDTO\CardUpdateDTO;
use App\Entity\UserInterface\Card\CardState;
use App\Entity\UserInterface\Card\CardView;
use App\Entity\UserInterface\Card\Colour;
use App\Entity\UserInterface\Icons;
use App\Repository\UserInterface\ORM\CardRepositories\CardColourRepositoryInterface;
use App\Repository\UserInterface\ORM\CardRepositories\CardStateRepositoryInterface;
use App\Repository\UserInterface\ORM\IconsRepositoryInterface;
use App\Traits\ValidatorProcessorTrait;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CardViewUpdateFacade implements CardViewUpdateInterface
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

    #[ArrayShape(["validationErrors"])]
    public function updateAllCardViewObjectProperties(CardUpdateDTO $cardUpdateDTO, CardView $cardView): array
    {
        if ($cardUpdateDTO->getCardColourID() !== null) {
            $cardColour = $this->cardColourRepository->find($cardUpdateDTO->getCardColourID());
            if (!$cardColour instanceof Colour) {
                $errors[] = 'Colour not found';
            } else {
                $cardView->setCardColourID($cardColour);
            }
        }

        if ($cardUpdateDTO->getCardIconID() !== null) {
            $cardIcon = $this->iconsRepository->find($cardUpdateDTO->getCardIconID());
            if (!$cardIcon instanceof Icons) {
                $errors[] = 'Icon not found';
            } else {
                $cardView->setCardIconID($cardIcon);
            }
        }

        if ($cardUpdateDTO->getCardStateID()) {
            $cardState = $this->cardStateRepository->find($cardUpdateDTO->getCardStateID());
            if (!$cardState instanceof CardState) {
                $errors[] = 'Card State state not found';
            } else {
                $cardView->setCardStateID($cardState);
            }
        }

        return array_merge($this->validateCardViewObject($cardView), $errors ?? []);
    }

    private function validateCardViewObject(CardView $cardView): array
    {
        $constraintViolationList = $this->validator->validate($cardView);

        if ($this->checkIfErrorsArePresent($constraintViolationList)) {
            return $this->getValidationErrorAsArray($constraintViolationList);
        }

        return [];
    }

}
