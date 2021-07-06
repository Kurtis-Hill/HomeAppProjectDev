<?php

namespace App\DataFixtures\ESP8266;

use App\DataFixtures\Core\RoomFixtures;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Core\Room;
use App\Entity\Devices\Devices;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ESP8266DeviceFixtures extends Fixture implements OrderedFixtureInterface
{
    public const LOGIN_TEST_ACCOUNT_NAME = [
        'name' => 'apiLoginTest',
        'password' => 'device1234'
    ];

    public const PERMISSION_CHECK_DEVICES = [
        //admin owned devices
      'AdminDeviceAdminRoomAdminGroup' => [
          'referenceName' => 'aaa',
          'password' => 'processSensorReadingUpdateRequest'
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

    public const INTERFACE_TEST_DATA = [

    ];

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getOrder()
    {
        return 3;
    }

    public function load(ObjectManager $manager)
    {
        //these devices are for permission checks one device for each scenario
        //Create a Admin Owned Device Admin Group Admin Room
        $adminAdminAdmin = new Devices();

        $adminAdminAdmin->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminAdminAdmin->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $adminAdminAdmin->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $adminAdminAdmin->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName']);
        $adminAdminAdmin->setPassword($this->passwordEncoder->encodePassword($adminAdminAdmin, self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['password']));
        $adminAdminAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName'], $adminAdminAdmin);
        $manager->persist($adminAdminAdmin);

//      Create Admin Owned Device Admin Group Regular Room
        $adminAdminRegular = new Devices();

        $adminAdminRegular->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminAdminRegular->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $adminAdminRegular->setRoomObject($this->getReference(RoomFixtures::REGULAR_ROOM));
        $adminAdminRegular->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomRegularGroup']['referenceName']);
        $adminAdminRegular->setPassword($this->passwordEncoder->encodePassword($adminAdminRegular, self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomRegularGroup']['password']));
        $adminAdminRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomRegularGroup']['referenceName'], $adminAdminRegular);
        $manager->persist($adminAdminRegular);


        //Create Admin Owned Device Regular Group Regular Room
        $adminRegularRegular = new Devices();

        $adminRegularRegular->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminRegularRegular->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $adminRegularRegular->setRoomObject($this->getReference(RoomFixtures::REGULAR_ROOM));
        $adminRegularRegular->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomRegularGroup']['referenceName']);
        $adminRegularRegular->setPassword($this->passwordEncoder->encodePassword($adminRegularRegular, self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomRegularGroup']['password']));
        $adminRegularRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomRegularGroup']['referenceName'], $adminRegularRegular);
        $manager->persist($adminRegularRegular);


        //Create Admin Owned Device Regular Group Admin Room
        $adminRegularAdmin = new Devices();

        $adminRegularAdmin->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $adminRegularAdmin->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $adminRegularAdmin->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $adminRegularAdmin->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomAdminGroup']['referenceName']);
        $adminRegularAdmin->setPassword($this->passwordEncoder->encodePassword($adminRegularAdmin, self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomAdminGroup']['password']));
        $adminRegularAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomAdminGroup']['referenceName'], $adminRegularRegular);
        $manager->persist($adminRegularAdmin);


        // Regular Device Regular Group Regular Room
        $regularRegularRegular = new Devices();

        $regularRegularRegular->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularRegularRegular->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $regularRegularRegular->setRoomObject($this->getReference(RoomFixtures::REGULAR_ROOM));
        $regularRegularRegular->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomRegularGroup']['referenceName']);
        $regularRegularRegular->setPassword($this->passwordEncoder->encodePassword($regularRegularRegular, self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomRegularGroup']['password']));
        $regularRegularRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomRegularGroup']['referenceName'], $regularRegularRegular);
        $manager->persist($regularRegularRegular);

//        Create Regular Device Regular Group Admin Room
        $regularRegularAdmin = new Devices();

        $regularRegularAdmin->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularRegularAdmin->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $regularRegularAdmin->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $regularRegularAdmin->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomAdminGroup']['referenceName']);
        $regularRegularAdmin->setPassword($this->passwordEncoder->encodePassword($regularRegularAdmin, self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomAdminGroup']['password']));
        $regularRegularAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomAdminGroup']['referenceName'], $regularRegularAdmin);
        $manager->persist($regularRegularAdmin);


        //Create Regular Device Admin Group Admin Room
        $regularAdminAdmin = new Devices();

        $regularAdminAdmin->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularAdminAdmin->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $regularAdminAdmin->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $regularAdminAdmin->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomAdminGroup']['referenceName']);
        $regularAdminAdmin->setPassword($this->passwordEncoder->encodePassword($regularAdminAdmin, self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomAdminGroup']['password']));
        $regularAdminAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomAdminGroup']['referenceName'], $regularAdminAdmin);
        $manager->persist($regularAdminAdmin);


        //Create Regular Device Admin Group Regular Room
        $regularAdminRegular = new Devices();

        $regularAdminRegular->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER));
        $regularAdminRegular->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $regularAdminRegular->setRoomObject($this->getReference(RoomFixtures::REGULAR_ROOM));
        $regularAdminRegular->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomRegularGroup']['referenceName']);
        $regularAdminRegular->setPassword($this->passwordEncoder->encodePassword($regularAdminRegular, self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomRegularGroup']['password']));
        $regularAdminRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomRegularGroup']['referenceName'], $regularAdminRegular);
        $manager->persist($regularAdminRegular);


        //For Admin Duplicate Check
        $duplicateCheck = new Devices();

        $duplicateCheck->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER));
        $duplicateCheck->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $duplicateCheck->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $duplicateCheck->setDeviceName(self::LOGIN_TEST_ACCOUNT_NAME['name']);
        $duplicateCheck->setPassword($this->passwordEncoder->encodePassword($duplicateCheck, self::LOGIN_TEST_ACCOUNT_NAME['password']));
        $duplicateCheck->setRoles([Devices::ROLE]);

        $manager->persist($duplicateCheck);

        //@TODO created some admin and regular user isolated (group name mapping) devices

        $manager->flush();


    }
}
