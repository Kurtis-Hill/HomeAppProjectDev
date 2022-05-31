<?php

namespace App\UserInterface\Services\Cards\CardViewUpdate;

use App\Common\Traits\ValidatorProcessorTrait;
use App\UserInterface\DTO\Internal\CardUpdateDTO\CardUpdateDTO;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;
use App\UserInterface\Repository\ORM\CardRepositories\CardColourRepositoryInterface;
use App\UserInterface\Repository\ORM\CardRepositories\CardStateRepositoryInterface;
use App\UserInterface\Repository\ORM\IconsRepositoryInterface;
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
            $cardColour = $this->cardColourRepository->findOneById($cardUpdateDTO->getCardColourID());
            if (!$cardColour instanceof CardColour) {
                $errors[] = 'Colour not found';
            } else {
                $cardView->setCardColourID($cardColour);
            }
        }

        if ($cardUpdateDTO->getCardIconID() !== null) {
            $cardIcon = $this->iconsRepository->findOneById($cardUpdateDTO->getCardIconID());
            if (!$cardIcon instanceof Icons) {
                $errors[] = 'Icon not found';
            } else {
                $cardView->setCardIconID($cardIcon);
            }
        }

        if ($cardUpdateDTO->getCardStateID()) {
            $cardState = $this->cardStateRepository->findOneById($cardUpdateDTO->getCardStateID());
            if (!$cardState instanceof Cardstate) {
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

        return $this->getValidationErrorAsArray($constraintViolationList);
    }

}
