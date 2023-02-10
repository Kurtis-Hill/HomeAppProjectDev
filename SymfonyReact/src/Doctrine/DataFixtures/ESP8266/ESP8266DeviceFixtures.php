<?php

namespace App\Doctrine\DataFixtures\ESP8266;

use App\Doctrine\DataFixtures\Core\RoomFixtures;
use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Devices\Entity\Devices;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ESP8266DeviceFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURES_ORDER = 3;

    public const LOGIN_TEST_ACCOUNT_NAME = [
        'name' => 'apiLoginTest',
        'password' => 'device1234'
    ];

    public const ADMIN_TEST_DEVICE = [
        'referenceName' => 'admin-device',
        'password' => 'admin-device'
    ];

    public const USER_TEST_DEVICE = [
        'referenceName' => 'user-device',
        'password' => 'user-device'
    ];

    public const REGULAR_TEST_DEVICE = [
        'referenceName' => 'regular-device',
        'password' => 'regular-device'
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

    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getOrder(): int
    {
        return self::FIXTURES_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        //these devices are for permission checks one device for each scenario
        //Create a Admin Owned Device Admin Group Admin Room
        $adminAdminAdmin = new Devices();

        $adminAdminAdmin->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL));
        $adminAdminAdmin->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $adminAdminAdmin->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $adminAdminAdmin->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName']);
        $adminAdminAdmin->setPassword($this->passwordEncoder->hashPassword($adminAdminAdmin, self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['password']));
        $adminAdminAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName'], $adminAdminAdmin);
        $manager->persist($adminAdminAdmin);

//      Create Admin Owned Device Admin Group Regular Room
        $adminAdminRegular = new Devices();

        $adminAdminRegular->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL));
        $adminAdminRegular->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $adminAdminRegular->setRoomObject($this->getReference(RoomFixtures::REGULAR_ROOM));
        $adminAdminRegular->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomAdminGroup']['referenceName']);
        $adminAdminRegular->setPassword($this->passwordEncoder->hashPassword($adminAdminRegular, self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomAdminGroup']['password']));
        $adminAdminRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomAdminGroup']['referenceName'], $adminAdminRegular);
        $manager->persist($adminAdminRegular);


        //Create Admin Owned Device Regular Group Regular Room
        $adminRegularRegular = new Devices();

        $adminRegularRegular->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL));
        $adminRegularRegular->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $adminRegularRegular->setRoomObject($this->getReference(RoomFixtures::REGULAR_ROOM));
        $adminRegularRegular->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomRegularGroup']['referenceName']);
        $adminRegularRegular->setPassword($this->passwordEncoder->hashPassword($adminRegularRegular, self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomRegularGroup']['password']));
        $adminRegularRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminDeviceRegularRoomRegularGroup']['referenceName'], $adminRegularRegular);
        $manager->persist($adminRegularRegular);


        //Create Admin Owned Device Regular Group Admin Room
        $adminRegularAdmin = new Devices();

        $adminRegularAdmin->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL));
        $adminRegularAdmin->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $adminRegularAdmin->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $adminRegularAdmin->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomRegularGroup']['referenceName']);
        $adminRegularAdmin->setPassword($this->passwordEncoder->hashPassword($adminRegularAdmin, self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomRegularGroup']['password']));
        $adminRegularAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomRegularGroup']['referenceName'], $adminRegularRegular);
        $manager->persist($adminRegularAdmin);


        // Regular Device Regular Group Regular Room
        $regularRegularRegular = new Devices();

        $regularRegularRegular->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL));
        $regularRegularRegular->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $regularRegularRegular->setRoomObject($this->getReference(RoomFixtures::REGULAR_ROOM));
        $regularRegularRegular->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomRegularGroup']['referenceName']);
        $regularRegularRegular->setPassword($this->passwordEncoder->hashPassword($regularRegularRegular, self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomRegularGroup']['password']));
        $regularRegularRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomRegularGroup']['referenceName'], $regularRegularRegular);
        $manager->persist($regularRegularRegular);

//        Create Regular Device Regular Group Admin Room
        $regularRegularAdmin = new Devices();

        $regularRegularAdmin->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL));
        $regularRegularAdmin->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP));
        $regularRegularAdmin->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $regularRegularAdmin->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomRegularGroup']['referenceName']);
        $regularRegularAdmin->setPassword($this->passwordEncoder->hashPassword($regularRegularAdmin, self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomRegularGroup']['password']));
        $regularRegularAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomRegularGroup']['referenceName'], $regularRegularAdmin);
        $manager->persist($regularRegularAdmin);


        //Create Regular Device Admin Group Admin Room
        $regularAdminAdmin = new Devices();

        $regularAdminAdmin->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL));
        $regularAdminAdmin->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $regularAdminAdmin->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $regularAdminAdmin->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomAdminGroup']['referenceName']);
        $regularAdminAdmin->setPassword($this->passwordEncoder->hashPassword($regularAdminAdmin, self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomAdminGroup']['password']));
        $regularAdminAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularDeviceAdminRoomAdminGroup']['referenceName'], $regularAdminAdmin);
        $manager->persist($regularAdminAdmin);


        //Create Regular Device Admin Group Regular Room
        $regularAdminRegular = new Devices();

        $regularAdminRegular->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL));
        $regularAdminRegular->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $regularAdminRegular->setRoomObject($this->getReference(RoomFixtures::REGULAR_ROOM));
        $regularAdminRegular->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomAdminGroup']['referenceName']);
        $regularAdminRegular->setPassword($this->passwordEncoder->hashPassword($regularAdminRegular, self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomAdminGroup']['password']));
        $regularAdminRegular->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularDeviceRegularRoomAdminGroup']['referenceName'], $regularAdminRegular);
        $manager->persist($regularAdminRegular);


        //For Admin Duplicate Check
        $duplicateCheck = new Devices();

        $duplicateCheck->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL));
        $duplicateCheck->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $duplicateCheck->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $duplicateCheck->setDeviceName(self::LOGIN_TEST_ACCOUNT_NAME['name']);
        $duplicateCheck->setPassword($this->passwordEncoder->hashPassword($duplicateCheck, self::LOGIN_TEST_ACCOUNT_NAME['password']));
        $duplicateCheck->setRoles([Devices::ROLE]);

        $manager->persist($duplicateCheck);

        $adminDevice = new Devices();
        $adminDevice->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL));
        $adminDevice->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $adminDevice->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $adminDevice->setDeviceName(self::ADMIN_TEST_DEVICE['referenceName']);
        $adminDevice->setPassword($this->passwordEncoder->hashPassword($adminDevice, self::ADMIN_TEST_DEVICE['password']));
        $adminDevice->setRoles([Devices::ROLE]);
        $this->setReference(self::ADMIN_TEST_DEVICE['referenceName'], $adminDevice);

        $manager->persist($adminDevice);

        $userDevice = new Devices();
        $userDevice->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL));
        $userDevice->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP));
        $userDevice->setRoomObject($this->getReference(RoomFixtures::ADMIN_ROOM));
        $userDevice->setDeviceName(self::USER_TEST_DEVICE['referenceName']);
        $userDevice->setPassword($this->passwordEncoder->hashPassword($userDevice, self::USER_TEST_DEVICE['password']));
        $userDevice->setRoles([Devices::ROLE]);
        $this->setReference(self::USER_TEST_DEVICE['referenceName'], $userDevice);

        $manager->persist($userDevice);

        $manager->flush();

        $userDevice = new Devices();
        $userDevice->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL));
        $userDevice->setGroupNameObject($this->getReference(UserDataFixtures::USER_GROUP));
        $userDevice->setRoomObject($this->getReference(RoomFixtures::REGULAR_ROOM));
        $userDevice->setDeviceName(self::USER_TEST_DEVICE['referenceName']);
        $userDevice->setPassword($this->passwordEncoder->hashPassword($userDevice, self::USER_TEST_DEVICE['password']));
        $userDevice->setRoles([Devices::ROLE]);
        $this->setReference(self::REGULAR_TEST_DEVICE['referenceName'], $userDevice);

        $manager->persist($userDevice);

        $manager->flush();
    }
}
