<?php

namespace App\DataFixtures;

use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Core\Room;
use App\Entity\Devices\Devices;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DeviceFixtures extends Fixture implements OrderedFixtureInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $adminAddedRoom = new Room();

        $adminAddedRoom->setRoom('LivingRoom');
        $adminAddedRoom->setGroupNameID($this->getReference(UserDataFixtures::ADMIN_GROUP));

        $manager->persist($adminAddedRoom);
        // Admin Group Devices
        for ($i = 0; $i <5; ++$i) {
            $device = new Devices();

            $device->setDeviceName('device' .$i);
            $device->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
            $device->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
            $device->setRoles([Devices::ROLE]);
            $device->setRoomObject($adminAddedRoom);

            $manager->persist($device);
        }

        $regularAddedRoom = new Room();

        $regularAddedRoom->setRoom('BedRoom');
        $regularAddedRoom->setGroupNameID($this->getReference(UserDataFixtures::REGULAR_GROUP));

        $manager->persist($regularAddedRoom);

        // Regular Group Devices
        for ($i = 0; $i <5; ++$i) {
            $device = new Devices();

            $device->setDeviceName('device' .$i);
            $device->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
            $device->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
            $device->setRoles([Devices::ROLE]);
            $device->setRoomObject($regularAddedRoom);

            $manager->persist($device);
        }

        //Admin Room Regular user
        for ($i = 0; $i <2; ++$i) {
            $device = new Devices();

            $device->setDeviceName('device' .$i);
            $device->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
            $device->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
            $device->setRoles([Devices::ROLE]);
            $device->setRoomObject($adminAddedRoom);

            $manager->persist($device);
        }

        //Regular Room Admin user
        for ($i = 0; $i <2; ++$i) {
            $device = new Devices();

            $device->setDeviceName('device' .$i);
            $device->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
            $device->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
            $device->setRoles([Devices::ROLE]);
            $device->setRoomObject($regularAddedRoom);

            $manager->persist($device);
        }


        //Regualr Room Admin Group
        //Admin Room Regular user
        for ($i = 0; $i <2; ++$i) {
            $device = new Devices();

            $device->setDeviceName('device' .$i);
            $device->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
            $device->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
            $device->setRoles([Devices::ROLE]);
            $device->setRoomObject($regularAddedRoom);

            $manager->persist($device);
        }

        //Admin Room Regular Group Admin user
        for ($i = 0; $i <2; ++$i) {
            $device = new Devices();

            $device->setDeviceName('device' .$i);
            $device->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
            $device->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
            $device->setRoles([Devices::ROLE]);
            $device->setRoomObject($adminAddedRoom);

            $manager->persist($device);
        }


        $manager->flush();
    }

    public function foo()
    {

    }
}
