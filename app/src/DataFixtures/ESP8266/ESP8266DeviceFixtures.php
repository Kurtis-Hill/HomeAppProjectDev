<?php

namespace App\DataFixtures\ESP8266;

use App\DataFixtures\Core\RoomFixtures;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\Room;
use App\Entity\User\User;
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

    public const ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE = 'A1AG1';

    public const ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO = 'A1RG2';

    public const ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_ONE = 'A2AG1';

    public const ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO = 'A2AG2';

    public const REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE = 'R1RG1';

    public const REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO = 'R2RG2';

    public const REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE = 'R2AG1';

    public const PERMISSION_CHECK_DEVICES = [
        //admin owned devices
        self::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE => [
          'referenceName' => self::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE,
          'password' => 'processSensorReadingUpdateRequest'
        ],
        self::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO => [
            'referenceName' => self::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO,
            'password' => 'device1234'
        ],
        self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_ONE => [
            'referenceName' => self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_ONE,
            'password' => 'device1234'
        ],
        self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO => [
            'referenceName' => self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO,
            'password' => 'device1234'
        ],
        //Regular user owned devices
        self::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE => [
            'referenceName' => self::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE,
            'password' => 'device1234'
        ],
        self::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO => [
            'referenceName' => self::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO,
            'password' => 'device1234'
        ],
        self::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE => [
            'referenceName' => self::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE,
            'password' => 'device1234'
        ],
    ];

    public const DEVICE_IP_ADDRESS = '192.168.1.100';

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
        $adminUserAdminGroupOne->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE, User::class));
        $adminUserAdminGroupOne->setGroupObject($this->getReference(UserDataFixtures::ADMIN_GROUP_ONE, Group::class));
        $adminUserAdminGroupOne->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM, Room::class));
        $adminUserAdminGroupOne->setDeviceName(self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]['referenceName']);
        $adminUserAdminGroupOne->setPassword($this->passwordEncoder->hashPassword($adminUserAdminGroupOne, self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]['password']));
        $adminUserAdminGroupOne->setRoles([Devices::ROLE]);
        $adminUserAdminGroupOne->setIpAddress(self::DEVICE_IP_ADDRESS);

        $this->addReference(self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]['referenceName'], $adminUserAdminGroupOne);
        $manager->persist($adminUserAdminGroupOne);


        //Create Admin One Owned Device Regular Group Two
        $adminOneRegularGroupTwo = new Devices();
        $adminOneRegularGroupTwo->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE, User::class));
        $adminOneRegularGroupTwo->setGroupObject($this->getReference(UserDataFixtures::REGULAR_GROUP_TWO, Group::class));
        $adminOneRegularGroupTwo->setRoomObject($this->getReference(RoomFixtures::BEDROOM_ONE, Room::class));
        $adminOneRegularGroupTwo->setDeviceName(self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO]['referenceName']);
        $adminOneRegularGroupTwo->setPassword($this->passwordEncoder->hashPassword($adminOneRegularGroupTwo, self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO]['password']));
        $adminOneRegularGroupTwo->setRoles([Devices::ROLE]);
        $adminOneRegularGroupTwo->setIpAddress(self::DEVICE_IP_ADDRESS);

        $this->addReference(self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO]['referenceName'], $adminOneRegularGroupTwo);
        $manager->persist($adminOneRegularGroupTwo);

        //Create Admin Two Owned Device Admin Group One
        $adminTwoDeviceAdminGroupOne = new Devices();
        $adminTwoDeviceAdminGroupOne->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_TWO, User::class));
        $adminTwoDeviceAdminGroupOne->setGroupObject($this->getReference(UserDataFixtures::ADMIN_GROUP_ONE, Group::class));
        $adminTwoDeviceAdminGroupOne->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM, Room::class));
        $adminTwoDeviceAdminGroupOne->setDeviceName(self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_ONE]['referenceName']);
        $adminTwoDeviceAdminGroupOne->setPassword($this->passwordEncoder->hashPassword($adminTwoDeviceAdminGroupOne, self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_ONE]['password']));
        $adminTwoDeviceAdminGroupOne->setRoles([Devices::ROLE]);
        $adminTwoDeviceAdminGroupOne->setIpAddress(self::DEVICE_IP_ADDRESS);

        $this->addReference(self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_ONE]['referenceName'], $adminTwoDeviceAdminGroupOne);
        $manager->persist($adminTwoDeviceAdminGroupOne);

        //Create Admin Two Owned Device Admin Group Two
        $adminTwoDeviceAdminGroupTwo = new Devices();
        $adminTwoDeviceAdminGroupTwo->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_TWO, User::class));
        $adminTwoDeviceAdminGroupTwo->setGroupObject($this->getReference(UserDataFixtures::ADMIN_GROUP_TWO, Group::class));
        $adminTwoDeviceAdminGroupTwo->setRoomObject($this->getReference(RoomFixtures::BEDROOM_ONE, Room::class));
        $adminTwoDeviceAdminGroupTwo->setDeviceName(self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO]['referenceName']);
        $adminTwoDeviceAdminGroupTwo->setPassword($this->passwordEncoder->hashPassword($adminTwoDeviceAdminGroupTwo, self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO]['password']));
        $adminTwoDeviceAdminGroupTwo->setRoles([Devices::ROLE]);
        $adminTwoDeviceAdminGroupTwo->setIpAddress(self::DEVICE_IP_ADDRESS);

        $this->addReference(self::PERMISSION_CHECK_DEVICES[self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO]['referenceName'], $adminTwoDeviceAdminGroupTwo);
        $manager->persist($adminTwoDeviceAdminGroupTwo);


        // Regular user one owned device regular group one
        $regularOwnedDeviceRegularGroupOne = new Devices();

        $regularOwnedDeviceRegularGroupOne->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL_ONE, User::class));
        $regularOwnedDeviceRegularGroupOne->setGroupObject($this->getReference(UserDataFixtures::REGULAR_GROUP_ONE, Group::class));
        $regularOwnedDeviceRegularGroupOne->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM, Room::class));
        $regularOwnedDeviceRegularGroupOne->setDeviceName(self::PERMISSION_CHECK_DEVICES[self::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE]['referenceName']);
        $regularOwnedDeviceRegularGroupOne->setPassword($this->passwordEncoder->hashPassword($regularOwnedDeviceRegularGroupOne, self::PERMISSION_CHECK_DEVICES[self::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE]['password']));
        $regularOwnedDeviceRegularGroupOne->setRoles([Devices::ROLE]);
        $regularOwnedDeviceRegularGroupOne->setIpAddress(self::DEVICE_IP_ADDRESS);

        $this->addReference(self::PERMISSION_CHECK_DEVICES[self::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE]['referenceName'], $regularOwnedDeviceRegularGroupOne);
        $manager->persist($regularOwnedDeviceRegularGroupOne);


        // Regular Owned Device Regular Group
        $regularTwoRegularGroupTwo = new Devices();

        $regularTwoRegularGroupTwo->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL_TWO, User::class));
        $regularTwoRegularGroupTwo->setGroupObject($this->getReference(UserDataFixtures::REGULAR_GROUP_TWO, Group::class));
        $regularTwoRegularGroupTwo->setRoomObject($this->getReference(RoomFixtures::BEDROOM_ONE, Room::class));
        $regularTwoRegularGroupTwo->setDeviceName(self::PERMISSION_CHECK_DEVICES[self::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO]['referenceName']);
        $regularTwoRegularGroupTwo->setPassword($this->passwordEncoder->hashPassword($regularTwoRegularGroupTwo, self::PERMISSION_CHECK_DEVICES[self::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO]['password']));
        $regularTwoRegularGroupTwo->setRoles([Devices::ROLE]);
        $regularTwoRegularGroupTwo->setIpAddress(self::DEVICE_IP_ADDRESS);

        $this->addReference(self::PERMISSION_CHECK_DEVICES[self::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO]['referenceName'], $regularTwoRegularGroupTwo);
        $manager->persist($regularTwoRegularGroupTwo);

//        Regular User Two Admin Group one
        $regularRegularAdmin = new Devices();

        $regularRegularAdmin->setCreatedBy($this->getReference(UserDataFixtures::REGULAR_USER_EMAIL_TWO, User::class));
        $regularRegularAdmin->setGroupObject($this->getReference(UserDataFixtures::ADMIN_GROUP_ONE, Group::class));
        $regularRegularAdmin->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM, Room::class));
        $regularRegularAdmin->setDeviceName(self::PERMISSION_CHECK_DEVICES[self::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE]['referenceName']);
        $regularRegularAdmin->setPassword($this->passwordEncoder->hashPassword($regularRegularAdmin, self::PERMISSION_CHECK_DEVICES[self::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE]['password']));
        $regularRegularAdmin->setRoles([Devices::ROLE]);
        $regularRegularAdmin->setIpAddress(self::DEVICE_IP_ADDRESS);

        $this->addReference(self::PERMISSION_CHECK_DEVICES[self::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE]['referenceName'], $regularRegularAdmin);
        $manager->persist($regularRegularAdmin);

//        //For Admin Duplicate Check
        $duplicateCheck = new Devices();

        $duplicateCheck->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE, User::class));
        $duplicateCheck->setGroupObject($this->getReference(UserDataFixtures::ADMIN_GROUP_ONE, Group::class));
        $duplicateCheck->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM, Room::class));
        $duplicateCheck->setDeviceName(self::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name']);
        $duplicateCheck->setPassword($this->passwordEncoder->hashPassword($duplicateCheck, self::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['password']));
        $duplicateCheck->setRoles([Devices::ROLE]);
        $duplicateCheck->setIpAddress(self::DEVICE_IP_ADDRESS);

        $manager->persist($duplicateCheck);

        // Admin General Test Device
        $adminDevice = new Devices();
        $adminDevice->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE, User::class));
        $adminDevice->setGroupObject($this->getReference(UserDataFixtures::ADMIN_GROUP_ONE, Group::class));
        $adminDevice->setRoomObject($this->getReference(RoomFixtures::LIVING_ROOM, Room::class));
        $adminDevice->setDeviceName(self::ADMIN_TEST_DEVICE['referenceName']);
        $adminDevice->setPassword($this->passwordEncoder->hashPassword($adminDevice, self::ADMIN_TEST_DEVICE['password']));
        $adminDevice->setRoles([Devices::ROLE]);
        $adminDevice->setIpAddress(self::DEVICE_IP_ADDRESS);

        $this->setReference(self::ADMIN_TEST_DEVICE['referenceName'], $adminDevice);
        $manager->persist($adminDevice);

        $manager->flush();
    }
}
