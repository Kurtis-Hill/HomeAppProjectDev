<?php

namespace App\DataFixtures\ESP8266;

use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Core\Room;
use App\Entity\Devices\Devices;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ESP8266DeviceFixtures extends Fixture implements OrderedFixtureInterface
{
    public const ADMIN_ROOM = 'admin-room';

    public const ADMIN_ROOM_NAME = 'LivingRoom';

    public const REGULAR_ROOM = 'regular-room';

    public const REGULAR_ROOM_NAME = 'BedRoom';

    public const LOGIN_TEST_ACCOUNT_NAME = [
        'name' => 'apiLoginTest',
        'password' => 'device1234'
    ];

    public const DEVICES = [
        //admin owned devices
      'AdminDeviceAdminRoomAdminGroup' => [
          'referenceName' => 'aaa',
          'password' => 'device1234'
      ],
        'AdminDeviceAdminRoomRegularGroup' => [
            'referenceName' => 'aar',
            'password' => 'device1234'
        ],
        'AdminDeviceRegularRoomRegularGroup' => [
            'referenceName' => 'arr',
            'password' => 'device1234'
        ],
        'AdminDeviceRegularRoomAdminGroup' => [
            'referenceName' => 'ara',
            'password' => 'device1234'
        ],

        //Regular user owned devices
        'RegularDeviceRegularRoomRegularGroup' => [
            'referenceName' => 'rrr',
            'password' => 'device1234'
        ],
        'RegularDeviceRegularRoomAdminGroup' => [
            'referenceName' => 'rra',
            'password' => 'device1234'
        ],
        'RegularDeviceAdminRoomAdminGroup' => [
            'referenceName' => 'raa',
            'password' => 'device1234'
        ],
        'RegularDeviceAdminRoomRegularGroup' => [
            'referenceName' => 'rar',
            'password' => 'device1234'
        ],

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

        //these devices are for permission checks one device for each scenario
        //Create a Admin Owned Device Admin Group Admin Room
        $adminAdminAdmin = new Devices();

        $adminAdminAdmin->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminAdminAdmin->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $adminAdminAdmin->setRoomObject($adminAddedRoom);
        $adminAdminAdmin->setDeviceName(self::DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName']);
        $adminAdminAdmin->setPassword($this->passwordEncoder->encodePassword($adminAdminAdmin, self::DEVICES['AdminDeviceAdminRoomAdminGroup']['password']));
        $adminAdminAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName'], $adminAdminAdmin);
        $manager->persist($adminAdminAdmin);

//      Create Admin Owned Device Admin Group Regular Room
        $adminAdminRegular = new Devices();

        $adminAdminRegular->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminAdminRegular->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $adminAdminRegular->setRoomObject($regularAddedRoom);
        $adminAdminRegular->setDeviceName(self::DEVICES['AdminDeviceAdminRoomRegularGroup']['referenceName']);
        $adminAdminRegular->setPassword($this->passwordEncoder->encodePassword($adminAdminRegular, self::DEVICES['AdminDeviceAdminRoomRegularGroup']['password']));
        $adminAdminRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::DEVICES['AdminDeviceAdminRoomRegularGroup']['referenceName'], $adminAdminRegular);
        $manager->persist($adminAdminRegular);


        //Create Admin Owned Device Regular Group Regular Room
        $adminRegularRegular = new Devices();

        $adminRegularRegular->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminRegularRegular->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $adminRegularRegular->setRoomObject($regularAddedRoom);
        $adminRegularRegular->setDeviceName(self::DEVICES['AdminDeviceRegularRoomRegularGroup']['referenceName']);
        $adminRegularRegular->setPassword($this->passwordEncoder->encodePassword($adminRegularRegular, self::DEVICES['AdminDeviceRegularRoomRegularGroup']['password']));
        $adminRegularRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::DEVICES['AdminDeviceRegularRoomRegularGroup']['referenceName'], $adminRegularRegular);
        $manager->persist($adminRegularRegular);


        //Create Admin Owned Device Regular Group Admin Room
        $adminRegularAdmin = new Devices();

        $adminRegularAdmin->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminRegularAdmin->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $adminRegularAdmin->setRoomObject($adminAddedRoom);
        $adminRegularAdmin->setDeviceName(self::DEVICES['AdminDeviceRegularRoomAdminGroup']['referenceName']);
        $adminRegularAdmin->setPassword($this->passwordEncoder->encodePassword($adminRegularAdmin, self::DEVICES['AdminDeviceRegularRoomAdminGroup']['password']));
        $adminRegularAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::DEVICES['AdminDeviceRegularRoomAdminGroup']['referenceName'], $adminRegularRegular);
        $manager->persist($adminRegularRegular);



        // Regular Device Regular Group Regular Room
        $regularRegularRegular = new Devices();

        $regularRegularRegular->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularRegularRegular->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $regularRegularRegular->setRoomObject($regularAddedRoom);
        $regularRegularRegular->setDeviceName(self::DEVICES['RegularDeviceRegularRoomRegularGroup']['referenceName']);
        $regularRegularRegular->setPassword($this->passwordEncoder->encodePassword($regularRegularRegular, self::DEVICES['RegularDeviceRegularRoomRegularGroup']['password']));
        $regularRegularRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::DEVICES['RegularDeviceRegularRoomRegularGroup']['referenceName'], $regularRegularRegular);
        $manager->persist($regularRegularRegular);

//        Create Regular Device Regular Group Admin Room
        $regularRegularAdmin = new Devices();

        $regularRegularAdmin->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularRegularAdmin->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $regularRegularAdmin->setRoomObject($adminAddedRoom);
        $regularRegularAdmin->setDeviceName(self::DEVICES['RegularDeviceRegularRoomAdminGroup']['referenceName']);
        $regularRegularAdmin->setPassword($this->passwordEncoder->encodePassword($regularRegularAdmin, self::DEVICES['RegularDeviceRegularRoomAdminGroup']['password']));
        $regularRegularAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::DEVICES['RegularDeviceRegularRoomAdminGroup']['referenceName'], $regularRegularAdmin);
        $manager->persist($regularRegularAdmin);


        //Create Regular Device Admin Group Admin Room
        $regularAdminAdmin = new Devices();

        $regularAdminAdmin->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularAdminAdmin->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $regularAdminAdmin->setRoomObject($adminAddedRoom);
        $regularAdminAdmin->setDeviceName(self::DEVICES['RegularDeviceAdminRoomAdminGroup']['referenceName']);
        $regularAdminAdmin->setPassword($this->passwordEncoder->encodePassword($regularAdminAdmin, self::DEVICES['RegularDeviceAdminRoomAdminGroup']['password']));
        $regularAdminAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::DEVICES['RegularDeviceAdminRoomAdminGroup']['referenceName'], $regularAdminAdmin);
        $manager->persist($regularAdminAdmin);


        //Create Regular Device Admin Group Regular Room
        $regularAdminRegular = new Devices();

        $regularAdminRegular->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularAdminRegular->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $regularAdminRegular->setRoomObject($regularAddedRoom);
        $regularAdminRegular->setDeviceName(self::DEVICES['RegularDeviceAdminRoomRegularGroup']['referenceName']);
        $regularAdminRegular->setPassword($this->passwordEncoder->encodePassword($regularAdminRegular, self::DEVICES['RegularDeviceAdminRoomRegularGroup']['password']));
        $regularAdminRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::DEVICES['RegularDeviceAdminRoomRegularGroup']['referenceName'], $regularAdminRegular);
        $manager->persist($regularAdminRegular);


        //For Admin Duplicate Check
        $duplicateCheck = new Devices();

        $duplicateCheck->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $duplicateCheck->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $duplicateCheck->setRoomObject($adminAddedRoom);
        $duplicateCheck->setDeviceName(self::LOGIN_TEST_ACCOUNT_NAME['name']);
        $duplicateCheck->setPassword($this->passwordEncoder->encodePassword($duplicateCheck, self::LOGIN_TEST_ACCOUNT_NAME['password']));
        $duplicateCheck->setRoles([Devices::ROLE]);

        $manager->persist($duplicateCheck);

        $manager->flush();
    }
}
