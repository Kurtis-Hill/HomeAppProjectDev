<?php

namespace App\UserInterface\Services\Cards\CardViewUpdateService;

use App\Common\Traits\ValidatorProcessorTrait;
use App\UserInterface\DTO\CardUpdateDTO\StandardCardUpdateDTO;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;
use App\UserInterface\Repository\ORM\CardRepositories\CardColourRepositoryInterface;
use App\UserInterface\Repository\ORM\CardRepositories\CardStateRepositoryInterface;
use App\UserInterface\Repository\ORM\IconsRepositoryInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;

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

    #[ArrayShape(["errors"])]
    public function updateAllCardViewObjectProperties(StandardCardUpdateDTO $cardUpdateDTO, CardView $cardView): array
    {
        try {
            $cardColour = $this->cardColourRepository->findOneById($cardUpdateDTO->getCardColourID());
            if (!$cardColour instanceof CardColour) {
                $errors[] = 'Card colour not found';
            } else {
                $cardView->setCardColourID($cardColour);
            }
        } catch (TypeError) {
            $cardView->setCardColourID(null);
        }
        try {
            $cardIcon = $this->iconsRepository->findOneById($cardUpdateDTO->getCardIconID());
            if (!$cardIcon instanceof Icons) {
                $errors[] = 'Card icon not found';
            } else {
                $cardView->setCardIconID($cardIcon);
            }
        } catch (TypeError) {
            $cardView->setCardIconID(null);
        }
        try {
            $cardState = $this->cardStateRepository->findOneById($cardUpdateDTO->getCardStateID());
            if (!$cardState instanceof Cardstate) {
                $errors[] = 'Card state not found';
            } else {
                $cardView->setCardStateID($cardState);
            }
        } catch (TypeError) {
            $cardView->setCardStateID(null);
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
