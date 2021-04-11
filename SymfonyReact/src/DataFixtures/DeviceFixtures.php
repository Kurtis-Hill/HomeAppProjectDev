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
    public const ADMIN_ROOM = 'admin-room';

    public const ADMIN_ROOM_NAME = 'LivingRoom';

    public const REGULAR_ROOM = 'regular-room';

    public const REGULAR_ROOM_NAME = 'BedRoom';

    public const LOGIN_TEST_ACCOUNT_NAME = [
        'name' => 'apiLoginTest',
        'password' => 'device1234'
    ];

    private UserPasswordEncoderInterface $passwordEncoder;

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
        //Create a Admin Owned Admin Group Admin Room Device

        $adminAdminAdmin = new Devices();

        $adminAdminAdmin->setDeviceName('adminAdminAdmin');
        $adminAdminAdmin->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminAdminAdmin->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $adminAdminAdmin->setRoomObject($adminAddedRoom);
        $adminAdminAdmin->setPassword($this->passwordEncoder->encodePassword($adminAdminAdmin, 'device1234'));
        $adminAdminAdmin->setRoles([Devices::ROLE]);

        $manager->persist($adminAdminAdmin);

//        Create Admin Owned Admin Group Regular Room Device
        $adminAdminRegular = new Devices();

        $adminAdminRegular->setDeviceName('adminAdminRegular');
        $adminAdminRegular->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminAdminRegular->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $adminAdminRegular->setRoomObject($regularAddedRoom);
        $adminAdminRegular->setPassword($this->passwordEncoder->encodePassword($adminAdminRegular, 'device1234'));
        $adminAdminRegular->setRoles([Devices::ROLE]);

        $manager->persist($adminAdminRegular);


        //Create Admin Owned Regular Group Regular Room
        $adminRegularRegular = new Devices();

        $adminRegularRegular->setDeviceName('adminRegularRegular');
        $adminRegularRegular->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminRegularRegular->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $adminRegularRegular->setRoomObject($regularAddedRoom);
        $adminRegularRegular->setPassword($this->passwordEncoder->encodePassword($adminRegularRegular, 'device1234'));
        $adminRegularRegular->setRoles([Devices::ROLE]);

        $manager->persist($adminRegularRegular);


        $adminRegularAdmin = new Devices();

        $adminRegularAdmin->setDeviceName('adminRegularAdmin');
        $adminRegularAdmin->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminRegularAdmin->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $adminRegularAdmin->setRoomObject($adminAddedRoom);
        $adminRegularAdmin->setPassword($this->passwordEncoder->encodePassword($adminRegularAdmin, 'device1234'));
        $adminRegularAdmin->setRoles([Devices::ROLE]);

        $manager->persist($adminRegularAdmin);





        // Regular Device Regular Group Regular Room
        $regularRegularRegular = new Devices();

        $regularRegularRegular->setDeviceName('regularRegularRegula');
        $regularRegularRegular->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularRegularRegular->setPassword($this->passwordEncoder->encodePassword($regularRegularRegular, 'device1234'));
        $regularRegularRegular->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $regularRegularRegular->setRoles([Devices::ROLE]);
        $regularRegularRegular->setRoomObject($regularAddedRoom);

        $manager->persist($regularRegularRegular);

//        Create Regular Device Regular Group Admin Room
        $regularRegularAdmin = new Devices();

        $regularRegularAdmin->setDeviceName('regularRegularAdmin');
        $regularRegularAdmin->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularRegularAdmin->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $regularRegularAdmin->setRoomObject($adminAddedRoom);
        $regularRegularAdmin->setPassword($this->passwordEncoder->encodePassword($regularRegularAdmin, 'device1234'));
        $regularRegularAdmin->setRoles([Devices::ROLE]);

        $manager->persist($regularRegularAdmin);


        //Create Regular Device Admin Group Admin Room
        $regularAdminAdmin = new Devices();

        $regularAdminAdmin->setDeviceName('regularAdminAdmin');
        $regularAdminAdmin->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularAdminAdmin->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $regularAdminAdmin->setRoomObject($adminAddedRoom);
        $regularAdminAdmin->setPassword($this->passwordEncoder->encodePassword($regularAdminAdmin, 'device1234'));
        $regularAdminAdmin->setRoles([Devices::ROLE]);

        $manager->persist($regularAdminAdmin);


        //Create Regular Device Admin Group Regular Room
        $regularAdminRegular = new Devices();

        $regularAdminRegular->setDeviceName('regularAdminRegular');
        $regularAdminRegular->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularAdminRegular->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $regularAdminRegular->setRoomObject($regularAddedRoom);
        $regularAdminRegular->setPassword($this->passwordEncoder->encodePassword($regularAdminRegular, 'device1234'));
        $regularAdminRegular->setRoles([Devices::ROLE]);

        $manager->persist($regularAdminRegular);



        //For Admin Duplicate Check
        $duplicateCheck = new Devices();

        $duplicateCheck->setDeviceName(self::LOGIN_TEST_ACCOUNT_NAME['name']);
        $duplicateCheck->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $duplicateCheck->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $duplicateCheck->setRoomObject($adminAddedRoom);
        $duplicateCheck->setPassword($this->passwordEncoder->encodePassword($duplicateCheck, self::LOGIN_TEST_ACCOUNT_NAME['password']));
        $duplicateCheck->setRoles([Devices::ROLE]);

        $manager->persist($duplicateCheck);



//
//        $manager->persist($regularAddedRoom);
//
//        // Regular Group Devices
//        for ($i = 0; $i <5; ++$i) {
//            $device = new Devices();
//
//            $device->setDeviceName('device' .$i);
//            $device->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
//            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
//            $device->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
//            $device->setRoles([Devices::ROLE]);
//            $device->setRoomObject($regularAddedRoom);
//
//            $manager->persist($device);
//        }
//
//        //Admin Room Regular user
//        for ($i = 0; $i <2; ++$i) {
//            $device = new Devices();
//
//            $device->setDeviceName('device' .$i);
//            $device->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
//            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
//            $device->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
//            $device->setRoles([Devices::ROLE]);
//            $device->setRoomObject($adminAddedRoom);
//
//            $manager->persist($device);
//        }
//
//        //Regular Room Admin user
//        for ($i = 0; $i <2; ++$i) {
//            $device = new Devices();
//
//            $device->setDeviceName('device' .$i);
//            $device->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
//            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
//            $device->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
//            $device->setRoles([Devices::ROLE]);
//            $device->setRoomObject($regularAddedRoom);
//
//            $manager->persist($device);
//        }
//
//
//        //Regualr Room Admin Group
//        //Admin Room Regular user
//        for ($i = 0; $i <2; ++$i) {
//            $device = new Devices();
//
//            $device->setDeviceName('device' .$i);
//            $device->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
//            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
//            $device->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
//            $device->setRoles([Devices::ROLE]);
//            $device->setRoomObject($regularAddedRoom);
//
//            $manager->persist($device);
//        }
//
//        //Admin Room Regular Group Admin user
//        for ($i = 0; $i <2; ++$i) {
//            $device = new Devices();
//
//            $device->setDeviceName('device' .$i);
//            $device->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
//            $device->setPassword($this->passwordEncoder->encodePassword($device, 'device1234'.$i));
//            $device->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
//            $device->setRoles([Devices::ROLE]);
//            $device->setRoomObject($adminAddedRoom);
//
//            $manager->persist($device);
//        }


        $manager->flush();
    }

    public function foo()
    {

    }
}
