<?php

namespace App\UserInterface\Services\Cards\CardCreation;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\CardViewObjectBuilder\CardViewObjectBuilder;
use App\Sensors\Entity\Sensor;
use App\User\Entity\User;
use App\UserInterface\DTO\Internal\NewCard\NewCardOptionsDTO;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\CardState;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;
use App\UserInterface\Exceptions\CardColourException;
use App\UserInterface\Exceptions\CardStateException;
use App\UserInterface\Exceptions\IconException;
use App\UserInterface\Repository\ORM\CardRepositories\CardColourRepositoryInterface;
use App\UserInterface\Repository\ORM\CardRepositories\CardStateRepositoryInterface;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepositoryInterface;
use App\UserInterface\Repository\ORM\IconsRepositoryInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CardCreationHandler implements CardCreationHandlerInterface
{
    use ValidatorProcessorTrait;

    public const SENSOR_ALREADY_EXISTS = 'You Already have a card view for this sensor';

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
    #[ArrayShape(['errors'])]
    public function createUserCardForSensor(Sensor $sensorObject, User $user, ?NewCardOptionsDTO $cardOptionsDTO = null): array
    {
        try {
            if ($cardOptionsDTO !== null && $cardOptionsDTO->getIconID() !== null) {
                $icon = $this->iconsRepository->find($cardOptionsDTO?->getIconID());
            }

            $icon = $icon ?? $this->generateRandomIconObject();

            if ($cardOptionsDTO !== null && $cardOptionsDTO?->getColourID() !== null) {
                $colour = $this->cardColourRepository->find($cardOptionsDTO?->getColourID());
            }
            $colour = $colour ?? $this->generateRandomColourObject();

            if ($cardOptionsDTO !== null && $cardOptionsDTO?->getStateID() !== null) {
                $onCardState = $this->cardStateRepository->find($cardOptionsDTO?->getStateID());
            }
            $onCardState = $onCardState ?? $this->generateOnStateCardObject();

        } catch (IconException | CardColourException | CardStateException $e) {
            return [$e->getMessage()];
        } catch (NonUniqueResultException | NoResultException) {
            return ['There is an issue with the database contact an administrator'];
        }

        $newCard = CardViewObjectBuilder::buildNewCardViewObject(
            $sensorObject,
            $user,
            $icon,
            $colour,
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
        } catch (UniqueConstraintViolationException) {
            return [self::SENSOR_ALREADY_EXISTS];
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
        $firstColourId = $this->cardColourRepository->getFirstColourID()->getColourID();
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
    private function generateOnStateCardObject(): CardState
    {
        $onCardState = $this->cardStateRepository->findOneByState(CardState::ON);

        if (!$onCardState instanceof CardState) {
            throw new CardStateException(CardStateException::CARD_STATE_NOT_FOUND);
        }

        return $onCardState;
    }

    private function validateNewCard(CardView $cardView): array
    {
        $errors = $this->validator->validate($cardView);

        if ($this->checkIfErrorsArePresent($errors)) {
            return $this->getValidationErrorAsArray($errors);
        }

        return [];
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
