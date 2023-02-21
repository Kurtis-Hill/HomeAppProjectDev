<?php

namespace App\ORM\DataFixtures\ESP8266;

use App\ORM\DataFixtures\Core\RoomFixtures;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Devices\Entity\Devices;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ESP8266DeviceFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 3;

    public const LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE = [
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
        'AdminUserOneDeviceAdminGroupOne' => [
          'referenceName' => 'AdminUserOneDeviceAdminGroupOne',
          'password' => 'processSensorReadingUpdateRequest'
        ],
        'AdminUserOneDeviceRegularGroupTwo' => [
            'referenceName' => 'AdminUserOneDeviceRegularGroupTwo',
            'password' => 'device1234'
        ],
        'AdminUserTwoDeviceAdminGroupTwo' => [
            'referenceName' => 'AdminUserTwoDeviceAdminGroupTwo',
            'password' => 'device1234'
        ],
        //Regular user owned devices
        'RegularUserOneDeviceRegularGroupOne' => [
            'referenceName' => 'RegularUserOneDeviceRegularGroupOne',
            'password' => 'device1234'
        ],
        'RegularUserTwoDeviceRegularGroupTwo' => [
            'referenceName' => 'RegularUserTwoDeviceRegularGroupTwo',
            'password' => 'device1234'
        ],
        'RegularUserTwoDeviceAdminGroupOne' => [
            'referenceName' => 'RegularUserTwoDeviceAdminGroupOne',
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
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        //these devices are for permission checks one device for each scenario
        //Create a Admin Owned Device Admin Group
        $adminUserAdminGroupOne = new Devices();
        $adminUserAdminGroupOne->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE));
        $adminUserAdminGroupOne->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP_ONE));
        $adminUserAdminGroupOne->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM));
        $adminUserAdminGroupOne->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceAdminGroupOne']['referenceName']);
        $adminUserAdminGroupOne->setPassword($this->passwordEncoder->hashPassword($adminUserAdminGroupOne, self::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceAdminGroupOne']['password']));
        $adminUserAdminGroupOne->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceAdminGroupOne']['referenceName'], $adminUserAdminGroupOne);
        $manager->persist($adminUserAdminGroupOne);


        //Create Admin One Owned Device Regular Group Two
        $adminOneRegularGroupTwo = new Devices();
        $adminOneRegularGroupTwo->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE));
        $adminOneRegularGroupTwo->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP_TWO));
        $adminOneRegularGroupTwo->setRoomObject($this->getReference(RoomFixtures::BEDROOM_ONE));
        $adminOneRegularGroupTwo->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceRegularGroupTwo']['referenceName']);
        $adminOneRegularGroupTwo->setPassword($this->passwordEncoder->hashPassword($adminOneRegularGroupTwo, self::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceRegularGroupTwo']['password']));
        $adminOneRegularGroupTwo->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceRegularGroupTwo']['referenceName'], $adminOneRegularGroupTwo);
        $manager->persist($adminOneRegularGroupTwo);


        //Create Admin Two Owned Device Admin Group Two
        $adminTwoDeviceAdminGroupTwo = new Devices();
        $adminTwoDeviceAdminGroupTwo->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_TWO));
        $adminTwoDeviceAdminGroupTwo->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP_TWO));
        $adminTwoDeviceAdminGroupTwo->setRoomObject($this->getReference(RoomFixtures::BEDROOM_ONE));
        $adminTwoDeviceAdminGroupTwo->setDeviceName(self::PERMISSION_CHECK_DEVICES['AdminUserTwoDeviceAdminGroupTwo']['referenceName']);
        $adminTwoDeviceAdminGroupTwo->setPassword($this->passwordEncoder->hashPassword($adminTwoDeviceAdminGroupTwo, self::PERMISSION_CHECK_DEVICES['AdminUserTwoDeviceAdminGroupTwo']['password']));
        $adminTwoDeviceAdminGroupTwo->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['AdminUserTwoDeviceAdminGroupTwo']['referenceName'], $adminTwoDeviceAdminGroupTwo);
        $manager->persist($adminTwoDeviceAdminGroupTwo);


        // Regular user one owned device regular group one
        $regularOwnedDeviceRegularGroupOne = new Devices();

        $regularOwnedDeviceRegularGroupOne->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL_ONE));
        $regularOwnedDeviceRegularGroupOne->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP_ONE));
        $regularOwnedDeviceRegularGroupOne->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM));
        $regularOwnedDeviceRegularGroupOne->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularUserOneDeviceRegularGroupOne']['referenceName']);
        $regularOwnedDeviceRegularGroupOne->setPassword($this->passwordEncoder->hashPassword($regularOwnedDeviceRegularGroupOne, self::PERMISSION_CHECK_DEVICES['RegularUserOneDeviceRegularGroupOne']['password']));
        $regularOwnedDeviceRegularGroupOne->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularUserOneDeviceRegularGroupOne']['referenceName'], $regularOwnedDeviceRegularGroupOne);
        $manager->persist($regularOwnedDeviceRegularGroupOne);


        // Regular Owned Device Regular Group
        $regularTwoRegularGroupTwo = new Devices();

        $regularTwoRegularGroupTwo->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL_TWO));
        $regularTwoRegularGroupTwo->setGroupNameObject($this->getReference(UserDataFixtures::REGULAR_GROUP_TWO));
        $regularTwoRegularGroupTwo->setRoomObject($this->getReference(RoomFixtures::BEDROOM_ONE));
        $regularTwoRegularGroupTwo->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceRegularGroupTwo']['referenceName']);
        $regularTwoRegularGroupTwo->setPassword($this->passwordEncoder->hashPassword($regularTwoRegularGroupTwo, self::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceRegularGroupTwo']['password']));
        $regularTwoRegularGroupTwo->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceRegularGroupTwo']['referenceName'], $regularTwoRegularGroupTwo);
        $manager->persist($regularTwoRegularGroupTwo);

//        Regular User Two Admin Group one
        $regularRegularAdmin = new Devices();

        $regularRegularAdmin->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL_TWO));
        $regularRegularAdmin->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP_ONE));
        $regularRegularAdmin->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM));
        $regularRegularAdmin->setDeviceName(self::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceAdminGroupOne']['referenceName']);
        $regularRegularAdmin->setPassword($this->passwordEncoder->hashPassword($regularRegularAdmin, self::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceAdminGroupOne']['password']));
        $regularRegularAdmin->setRoles([Devices::ROLE]);

        $this->addReference(self::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceAdminGroupOne']['referenceName'], $regularRegularAdmin);
        $manager->persist($regularRegularAdmin);

//        //For Admin Duplicate Check
        $duplicateCheck = new Devices();

        $duplicateCheck->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE));
        $duplicateCheck->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP_ONE));
        $duplicateCheck->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM));
        $duplicateCheck->setDeviceName(self::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name']);
        $duplicateCheck->setPassword($this->passwordEncoder->hashPassword($duplicateCheck, self::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['password']));
        $duplicateCheck->setRoles([Devices::ROLE]);

        $manager->persist($duplicateCheck);

        // Admin General Test Device
        $adminDevice = new Devices();
        $adminDevice->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE));
        $adminDevice->setGroupNameObject($this->getReference(UserDataFixtures::ADMIN_GROUP_ONE));
        $adminDevice->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM));
        $adminDevice->setDeviceName(self::ADMIN_TEST_DEVICE['referenceName']);
        $adminDevice->setPassword($this->passwordEncoder->hashPassword($adminDevice, self::ADMIN_TEST_DEVICE['password']));
        $adminDevice->setRoles([Devices::ROLE]);
        $this->setReference(self::ADMIN_TEST_DEVICE['referenceName'], $adminDevice);

        $manager->persist($adminDevice);

        $manager->flush();
    }
}
