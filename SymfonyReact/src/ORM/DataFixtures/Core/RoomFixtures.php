<?php

namespace App\ORM\DataFixtures\Core;

use App\User\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RoomFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 2;

    public const LIVING_ROOM = 'living-room';

    public const BEDROOM_ONE = 'bedroom-one';

    public const ROOMS = [
      self::LIVING_ROOM,
      self::BEDROOM_ONE
    ];

    public function load(ObjectManager $manager): void
    {
        $adminAddedRoom = new Room();
        $adminAddedRoom->setRoom(self::LIVING_ROOM);

        $manager->persist($adminAddedRoom);
        $this->addReference(self::LIVING_ROOM, $adminAddedRoom);

        $regularAddedRoom = new Room();
        $regularAddedRoom->setRoom(self::BEDROOM_ONE);

        $manager->persist($regularAddedRoom);
        $this->addReference(self::BEDROOM_ONE, $regularAddedRoom);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }
}
