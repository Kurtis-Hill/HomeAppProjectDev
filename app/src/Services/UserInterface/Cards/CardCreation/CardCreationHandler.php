<?php

namespace App\Services\UserInterface\Cards\CardCreation;

use App\Builders\UserInterface\CardViewObjectBuilder\CardViewObjectBuilder;
use App\DTOs\UserInterface\Internal\NewCard\NewCardOptionsDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardState;
use App\Entity\UserInterface\Card\CardView;
use App\Entity\UserInterface\Card\Colour;
use App\Entity\UserInterface\Icons;
use App\Exceptions\UserInterface\CardColourException;
use App\Exceptions\UserInterface\CardStateException;
use App\Exceptions\UserInterface\IconException;
use App\Repository\UserInterface\ORM\CardRepositories\CardColourRepositoryInterface;
use App\Repository\UserInterface\ORM\CardRepositories\CardStateRepositoryInterface;
use App\Repository\UserInterface\ORM\CardRepositories\CardViewRepositoryInterface;
use App\Repository\UserInterface\ORM\IconsRepositoryInterface;
use App\Traits\ValidatorProcessorTrait;
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
                $icon = $this->iconsRepository->find($cardOptionsDTO->getIconID());
            }

            $icon = $icon ?? $this->generateRandomIconObject();

            if ($cardOptionsDTO !== null && $cardOptionsDTO->getColourID() !== null) {
                $colour = $this->cardColourRepository->find($cardOptionsDTO->getColourID());
            }
            $colour = $colour ?? $this->generateRandomColourObject();

            if ($cardOptionsDTO !== null && $cardOptionsDTO->getStateID() !== null) {
                $onCardState = $this->cardStateRepository->find($cardOptionsDTO->getStateID());
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
    private function generateRandomColourObject(): Colour
    {
        $maxColourNumber = $this->cardColourRepository->countAllColours();
        $firstColourId = $this->cardColourRepository->getFirstColourID()->getColourID();
        $randomColour = $this->cardColourRepository->findOneBy(['colourID' => random_int($firstColourId, $maxColourNumber + $firstColourId -1)]);

        if (!$randomColour instanceof Colour) {
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
     * @throws OptimisticLockException
     * @throws UniqueConstraintViolationException
     */
    private function saveNewCard(CardView $cardView): void
    {
        $this->cardViewRepository->persist($cardView);
        $this->cardViewRepository->flush();
    }
}
