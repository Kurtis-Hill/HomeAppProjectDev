<?php

namespace App\UserInterface\Services\Cards\CardCreation;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\CardViewObjectBuilder\CardViewObjectBuilder;
use App\Sensors\Entity\Sensor;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;
use App\UserInterface\Exceptions\CardColourException;
use App\UserInterface\Exceptions\CardStateException;
use App\UserInterface\Exceptions\IconException;
use App\UserInterface\Repository\ORM\CardRepositories\CardColourRepositoryInterface;
use App\UserInterface\Repository\ORM\CardRepositories\CardStateRepositoryInterface;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepositoryInterface;
use App\UserInterface\Repository\ORM\IconsRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CardCreationFacade implements CardCreationHandlerInterface
{
    use ValidatorProcessorTrait;

    private CardViewRepositoryInterface $cardViewRepository;

    private CardColourRepositoryInterface $cardColourRepository;

    private CardStateRepositoryInterface$cardStateRepository;

    private IconsRepositoryInterface $iconsRepository;

    private ValidatorInterface $validator;

    public function __construct(
        CardViewRepositoryInterface $cardViewRepository,
        CardStateRepositoryInterface $cardStateRepository,
        CardColourRepositoryInterface $cardColourRepository,
        IconsRepositoryInterface $iconsRepository,
        ValidatorInterface $validator,
    ) {
        $this->cardViewRepository = $cardViewRepository;
        $this->cardStateRepository = $cardStateRepository;
        $this->cardColourRepository = $cardColourRepository;
        $this->iconsRepository = $iconsRepository;
        $this->validator = $validator;
    }


    /**
     * @throws CardStateException
     */
    public function createUserCardForSensor(Sensor $sensorObject, UserInterface $user): array
    {
        try {
            $randomIcon = $this->generateRandomIconObject();
            $randomColour = $this->generateRandomColourObject();
            $onCardState = $this->generateOnStateCardObject();
        } catch (IconException | CardColourException | CardStateException $e) {
            return [$e->getMessage()];
        } catch (NonUniqueResultException | NoResultException) {
            return ['There is an issue with the database contact an administrator'];
        }

        $newCard = CardViewObjectBuilder::buildNewCardViewObject(
            $sensorObject,
            $user,
            $randomIcon,
            $randomColour,
            $onCardState,
        );

        $errors = $this->validateNewCard($newCard);
        if (!empty($errors)) {
            return $errors;
        }

        try {
            $this->saveNewCard($newCard);
        } catch (ORMException | OptimisticLockException) {
            return ['Failed to save new card for sensor'];
        }

        return [];
    }

    /**
     * @throws IconException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function generateRandomIconObject(): Icons
    {
        $maxIconNumber = $this->iconsRepository->countAllIcons();
        $firstIconId = $this->iconsRepository->getFirstIcon()->getIconID();
        $randomIcon = $this->iconsRepository->findOneById(random_int($firstIconId, $firstIconId+$maxIconNumber-1));

        if (!$randomIcon instanceof Icons) {
            throw new IconException(IconException::FAILED_SETTING_RANDOM_ICON);
        }

        return $randomIcon;
    }

    /**
     * @throws CardColourException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function generateRandomColourObject(): CardColour
    {
        $maxColourNumber = $this->cardColourRepository->countAllColours();
        $firstColourId = $this->cardColourRepository->getFirstColourId()->getColourID();
        $randomColour = $this->cardColourRepository->findOneBy(['colourID' => random_int($firstColourId, $maxColourNumber + $firstColourId -1)]);

        if (!$randomColour instanceof CardColour) {
            throw new CardColourException(CardColourException::FAILED_SETTING_RANDOM_COLOUR);
        }

        return $randomColour;
    }

    /**
     * @throws CardStateException
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function generateOnStateCardObject(): Cardstate
    {
        $onCardState = $this->cardStateRepository->findOneByState(Cardstate::ON);

        if (!$onCardState instanceof Cardstate) {
            throw new CardStateException(CardStateException::CARD_STATE_NOT_FOUND);
        }

        return $onCardState;
    }

    private function validateNewCard(CardView $cardView): array
    {
        $errors = $this->validator->validate($cardView);

        return $this->getValidationErrorAsArray($errors);
    }

    /**
     * @throws ORMException
     */
    private function saveNewCard(CardView $cardView): void
    {
        $this->cardViewRepository->persist($cardView);
        $this->cardViewRepository->flush();
    }
}
