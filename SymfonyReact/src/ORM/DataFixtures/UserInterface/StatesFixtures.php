<?php

namespace App\ORM\DataFixtures\UserInterface;

use App\UserInterface\Entity\Card\Cardstate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StatesFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 6;

    public const CARD_STATES = [
        'on' => Cardstate::ON,
        'off' => Cardstate::OFF,
        'device' => Cardstate::DEVICE_ONLY,
        'room' => Cardstate::ROOM_ONLY,
    ];

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }
    public function load(ObjectManager $manager): void
    {
        foreach (self::CARD_STATES as $state) {
            $newCardState = new Cardstate();
            $newCardState->setState($state);

            $this->setReference($state, $newCardState);
            $manager->persist($newCardState);
        }

        $manager->flush();
    }
}
