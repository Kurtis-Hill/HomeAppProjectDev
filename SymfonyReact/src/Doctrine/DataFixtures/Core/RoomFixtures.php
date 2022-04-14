<?php


namespace App\AppConfig\DataFixtures\Core;


use App\User\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RoomFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURES_ORDER = 2;

    public const ADMIN_ROOM = 'admin-room';

    public const ADMIN_ROOM_NAME = 'LivingRoom';

    public const REGULAR_ROOM = 'regular-room';

    public const REGULAR_ROOM_NAME = 'BedRoom';

    public const ROOMS = [
      self::ADMIN_ROOM,
      self::REGULAR_ROOM
    ];

    public function load(ObjectManager $manager)
    {
        //Create an admin room
        $adminAddedRoom = new Room();
        $adminAddedRoom->setRoom(self::ADMIN_ROOM_NAME);
        $adminAddedRoom->setGroupNameID($this->getReference(UserDataFixtures::ADMIN_GROUP));

        $manager->persist($adminAddedRoom);
        $this->addReference(self::ADMIN_ROOM, $adminAddedRoom);
        //Create a Regular User Room
        $regularAddedRoom = new Room();
        $regularAddedRoom->setRoom(self::REGULAR_ROOM_NAME);
        $regularAddedRoom->setGroupNameID($this->getReference(UserDataFixtures::REGULAR_GROUP));

        $manager->persist($regularAddedRoom);
        $this->addReference(self::REGULAR_ROOM, $regularAddedRoom);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return self::FIXTURES_ORDER;
    }

}
