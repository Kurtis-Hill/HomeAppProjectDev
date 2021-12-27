<?php

namespace App\UserInterface\Services\Cards\CardCreation;

use App\ESPDeviceSensor\Entity\Sensor;
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
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CardCreationService implements CardCreationServiceInterface
{
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


    public function createUserCardForSensor(Sensor $sensorObject, UserInterface $user): array
    {
        try {
            $randomIcon = $this->returnRandomIcon();
            $randomColour = $this->returnRandomColour();
        } catch (IconException | CardColourException $e) {
            return [$e->getMessage()];
        } catch (NonUniqueResultException | NoResultException) {
            return ['There is an issue with the database contact an administrator'];
        }

        $onCardState = $this->cardStateRepository->findOneBy(['state' => Cardstate::ON]);

        if (!$onCardState instanceof Cardstate) {
            throw new CardStateException(CardStateException::CARD_STATE_NOT_FOUND);
        }

        $newCard = new CardView();
        $newCard->setSensorNameID($sensorObject);
        $newCard->setUserID($user);
        $newCard->setCardIconID($randomIcon);
        $newCard->setCardColourID($randomColour);
        $newCard->setCardStateID($onCardState);

        $errors = $this->validateNewCard($newCard);

        if (!empty($errors)) {
            return $errors;
        }

        try {
            $this->saveNewCard($newCard);
        } catch (ORMException) {
            return ['Failed to save new card for sensor'];
        }

        return [];
    }

    private function validateNewCard(CardView $cardView): array
    {
        $errors = $this->validator->validate($cardView);

        if (count($errors) > 0) {
            $validationErrors = [];
            foreach ($errors as $error) {
                $validationErrors[] = $error->getMessage();
            }

        }

        return $validationErrors ?? [];
    }

    /**
     * @throws ORMException
     */
    private function saveNewCard(CardView $cardView): void
    {
        $this->cardViewRepository->persist($cardView);
        $this->cardViewRepository->flush();
    }

    /**
     * @throws NonUniqueResultException
     * @throws IconException
     * @throws NoResultException
     */
    private function returnRandomIcon(): Icons
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
     * @throws NonUniqueResultException
     * @throws CardColourException
     * @throws NoResultException
     */
    private function returnRandomColour(): CardColour
    {
        $maxColourNumber = $this->cardColourRepository->countAllColours();
        $firstColourId = $this->cardColourRepository->getFirstColourId()->getColourID();
        $randomColour = $this->cardColourRepository->findOneBy(['colourID' => random_int($firstColourId, $maxColourNumber + $firstColourId -1)]);

        if (!$randomColour instanceof CardColour) {
            throw new CardColourException(CardColourException::FAILED_SETTING_RANDOM_COLOUR);
        }

        return $randomColour;
    }
}
