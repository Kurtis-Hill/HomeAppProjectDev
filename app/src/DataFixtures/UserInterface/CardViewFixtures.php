<?php

namespace App\DataFixtures\UserInterface;

use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\SensorFixtures;
use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardState;
use App\Entity\UserInterface\Card\CardView;
use App\Entity\UserInterface\Card\Colour;
use App\Entity\UserInterface\Icons;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CardViewFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 9;

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        //need to add selection for front end card view checks
        $amountOfColours = count(ColourFixtures::COLOURS) - 1;
        $amountOfIcons = count(IconFixtures::ICONS) - 1;

        // Admin user has a card for each sensor they own in ON state
        foreach (SensorFixtures::ADMIN_USER_ONE_OWNED_SENSORS as $sensor) {
            $newCard = new CardView();
            $newCard->setSensor($this->getReference(SensorFixtures::PERMISSION_CHECK_SENSORS[$sensor]['sensorName'], Sensor::class));
            $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE, User::class));
            $newCard->setCardStateID($this->getReference(StatesFixtures::CARD_STATES['on'], CardState::class));
            $newCard->setCardColourID($this->getReference(ColourFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour'], Colour::class));
            $newCard->setCardIconID($this->getReference(IconFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name'], Icons::class));

            $manager->persist($newCard);
        }

        // regular user has a card for each sensor they own in ON state
        foreach (SensorFixtures::REGULAR_USER_ONE_OWNED_SENSORS as $sensor) {
            $newCard = new CardView();
            $newCard->setSensor($this->getReference(SensorFixtures::PERMISSION_CHECK_SENSORS[$sensor]['sensorName'], Sensor::class));
            $newCard->setUserID($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL_ONE, User::class));
            $newCard->setCardStateID($this->getReference(StatesFixtures::CARD_STATES['on'], CardState::class));
            $newCard->setCardColourID($this->getReference(ColourFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour'], Colour::class));
            $newCard->setCardIconID($this->getReference(IconFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name'], Icons::class));

            $manager->persist($newCard);
        }

        // regular user has a card for each sensor they own in OFF state
        foreach (SensorFixtures::REGULAR_USER_TWO_OWNED_SENSORS as $sensor) {
            $newCard = new CardView();
            $newCard->setSensor($this->getReference(SensorFixtures::PERMISSION_CHECK_SENSORS[$sensor]['sensorName'], Sensor::class));
            $newCard->setUserID($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL_TWO, User::class));
            $newCard->setCardStateID($this->getReference(StatesFixtures::CARD_STATES['off'], CardState::class));
            $newCard->setCardColourID($this->getReference(ColourFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour'], Colour::class));
            $newCard->setCardIconID($this->getReference(IconFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name'], Icons::class));

            $manager->persist($newCard);
        }

        foreach (SensorFixtures::GROUP_ONE_SENSORS as $sensor) {
            $newCard = new CardView();
            $newCard->setSensor($this->getReference(SensorFixtures::PERMISSION_CHECK_SENSORS[$sensor]['sensorName'], Sensor::class));
            $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_TWO, User::class));
            $newCard->setCardStateID($this->getReference(StatesFixtures::CARD_STATES['device'], CardState::class));
            $newCard->setCardColourID($this->getReference(ColourFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour'], Colour::class));
            $newCard->setCardIconID($this->getReference(IconFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name'], Icons::class));

            $manager->persist($newCard);
        }

        foreach (SensorFixtures::GROUP_TWO_SENSORS as $sensor) {
            $newCard = new CardView();
            $newCard->setSensor($this->getReference(SensorFixtures::PERMISSION_CHECK_SENSORS[$sensor]['sensorName'], Sensor::class));
            $newCard->setUserID($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_TWO, User::class));
            $newCard->setCardStateID($this->getReference(StatesFixtures::CARD_STATES['room'], CardState::class));
            $newCard->setCardColourID($this->getReference(ColourFixtures::COLOURS[mt_rand(0, $amountOfColours)]['colour'], Colour::class));
            $newCard->setCardIconID($this->getReference(IconFixtures::ICONS[mt_rand(0, $amountOfIcons)]['name'], Icons::class));

            $manager->persist($newCard);
        }

        $manager->flush();
    }

}
