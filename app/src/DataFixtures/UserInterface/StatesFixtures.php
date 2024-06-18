<?php

namespace App\DataFixtures\UserInterface;

use App\Entity\UserInterface\Card\CardState;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StatesFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 6;

    public const CARD_STATES = [
        'on' => CardState::ON,
        'off' => CardState::OFF,
        'device' => CardState::DEVICE_ONLY,
        'room' => CardState::ROOM_ONLY,
    ];

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }
    public function load(ObjectManager $manager): void
    {
        foreach (self::CARD_STATES as $state) {
            $newCardState = new CardState();
            $newCardState->setState($state);

            $this->setReference($state, $newCardState);
            $manager->persist($newCardState);
        }

        $manager->flush();
    }
}
