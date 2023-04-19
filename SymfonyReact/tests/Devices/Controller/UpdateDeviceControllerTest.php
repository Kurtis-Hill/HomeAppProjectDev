<?php

namespace App\Tests\Devices\Controller;

use App\Common\API\HTTPStatusCodes;
use App\Devices\Controller\UpdateDeviceController;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Authentication\Controller\SecurityController;
use App\Authentication\Entity\GroupMapping;
use App\Common\API\APIErrorMessages;
use App\Devices\Entity\Devices;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateDeviceControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const UPDATE_DEVICE_URL = '/HomeApp/api/user/user-devices/%d/update-device';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private User $adminUser;

    private User $regularUserTwo;

    private DeviceRepositoryInterface $deviceRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken($this->client);

        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);

        $this->adminUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
    }

    public function test_sending_wrong_encoding_request(): void
    {
        /** @var Devices $device */
        $device = $this->deviceRepository->findBy(['groupID' => $this->adminUser->getGroup()])[0];

        $requestData = [
            'deviceName' => '$deviceName',
            'password' => '$password',
            'deviceGroup' => '$deviceGroup',
            'deviceRoom' => '$deviceRoom',
        ];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            implode(',', $requestData)
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals(APIErrorMessages::FORMAT_NOT_SUPPORTED, $responseData['title']);
    }

    /**
     * @dataProvider sendingWrongDataTypesToUpdateDataProvider
     */
    public function test_sending_wrong_data_types_to_update(
        mixed $deviceName,
        mixed $password,
        mixed $deviceGroup,
        mixed $deviceRoom,
        array $errorMessage,
    ): void {
        /** @var Devices $device */
        $device = $this->deviceRepository->findBy(['groupID' => $this->adminUser->getGroup()])[0];

        $requestData = [
            'deviceName' => $deviceName,
            'password' => $password,
            'deviceGroup' => $deviceGroup,
            'deviceRoom' => $deviceRoom,
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals($errorMessage, $responseData['errors']);
        self::assertEquals(APIErrorMessages::VALIDATION_ERRORS, $responseData['title']);
    }

    public function sendingWrongDataTypesToUpdateDataProvider(): Generator
    {
        yield [
            'deviceName' => [],
            'password' => 'NewPassword',
            'deviceGroup' => 1,
            'deviceRoom' => 1,
            'errorMessage' => [
                'deviceName must be of type string|null you provided array'
            ],
        ];

        yield [
            'deviceName' => 'newDeviceName',
            'password' => ['NewPassword'],
            'deviceGroup' => 1,
            'deviceRoom' => 1,
            'errorMessage' => [
                'password must be of type string|null you provided array'
            ],
        ];

        yield [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => 'deviceGroup',
            'deviceRoom' => 1,
            'errorMessage' => [
                'deviceGroup must be of type integer|null you provided "deviceGroup"'
            ],
        ];

        yield [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => 1,
            'deviceRoom' => 'deviceRoom',
            'errorMessage' => [
                'deviceRoom must be of type integer|null you provided "deviceRoom"'
            ],
        ];

        yield [
            'deviceName' => 1,
            'password' => ['NewPassword'],
            'deviceGroup' => [1],
            'deviceRoom' => 'deviceRoom',
            'errorMessage' => [
                'deviceName must be of type string|null you provided 1',
                'password must be of type string|null you provided array',
                'deviceGroup must be of type integer|null you provided array',
                'deviceRoom must be of type integer|null you provided "deviceRoom"',

            ],
        ];
    }

    public function test_sending_update_device_that_doesnt_exist(): void
    {
        while (true) {
            $nonExistentDeviceID = random_int(1, 100000);

            /** @var Devices $device */
            $device = $this->deviceRepository->findOneBy(['deviceID' => $nonExistentDeviceID]);
            if (!$device instanceof Devices) {
                break;
            }
        }

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $nonExistentDeviceID),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_sending_none_existent_roomID(): void
    {
        $roomRepository = $this->entityManager->getRepository(Room::class);
        while (true) {
            $nonExistentRoomID = random_int(1, 100000);

            /** @var Room $room */
            $room = $roomRepository->findOneBy(['roomID' => $nonExistentRoomID]);
            if (!$room instanceof Room) {
                break;
            }
        }
        /** @var Devices $device */
        $device = $this->deviceRepository->findBy(['groupID' => $this->adminUser->getGroup()])[0];

        if ($device === null) {
            self::fail('no device found for test');
        }

        $requestData = [
            'deviceName' => 'newDeviceName',
            'deviceGroup' => $this->adminUser->getGroup()->getGroupID(),
            'deviceRoom' => $nonExistentRoomID,
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        self::assertEquals(['Room not found for id ' . $nonExistentRoomID], $responseData['errors']);
        self::assertEquals(UpdateDeviceController::NOTHING_FOUND, $responseData['title']);
    }

    public function test_sending_none_existent_groupID(): void
    {
        $groupRepository = $this->entityManager->getRepository(Group::class);
        while (true) {
            $nonExistentGroupID = random_int(1, 100000);

            /** @var Group $group */
            $group = $groupRepository->findOneBy(['groupID' => $nonExistentGroupID]);
            if (!$group instanceof Room) {
                break;
            }
        }
        /** @var User $user */

        /** @var Devices $device */
        $device = $this->deviceRepository->findBy(['groupID' => $this->adminUser->getGroup()->getGroupID()])[0];

        $requestData = [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => $nonExistentGroupID,
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertEquals(['Group name not found for id ' . $nonExistentGroupID], $responseData['errors']);
        self::assertEquals(UpdateDeviceController::NOTHING_FOUND, $responseData['title']);
    }

    public function test_regular_user_cannot_update_device_group_not_apart_of(): void
    {
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $groupNameRepository = $this->entityManager->getRepository(Group::class);
        /** @var Group $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupIDs(),
        );

        /** @var Devices[] $devices */
        $devices = $this->entityManager->getRepository(Devices::class)->findBy(['groupID' => $groupsUserIsNotApartOf]);

        if (empty($devices)) {
            self::fail('no device found for test');
        }

        $device = $devices[0];
        $requestData = [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => $user->getGroup()->getGroupID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            $jsonPayload
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertEquals('You have been denied permission to perform this action', $responseData['errors'][0]);
        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

    }

    public function test_admin_can_update_device_not_apart_of(): void
    {
        $groupNameRepository = $this->entityManager->getRepository(Group::class);
        /** @var Group $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $groupNameRepository->findGroupsUserIsNotApartOf(
            $this->adminUser,
        );

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);
        if (empty($devices)) {
            self::fail('no device found for test');
        }

        $device = $devices[0];

        $newDeviceName = 'newDeviceName';
        $newPassword = 'NewPassword';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => $newPassword,
            'deviceGroup' => $this->adminUser->getGroup()->getGroupID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];
        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);
        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);
        self::assertEquals($this->adminUser->getGroup()->getGroupID(), $responseData['payload']['group']['groupID']);
        self::assertEquals($this->adminUser->getGroup()->getGroupName(), $responseData['payload']['group']['groupName']);
        self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
        self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
        self::assertEquals($newPassword, $responseData['payload']['secret']);
    }

    public function test_admin_can_update_device_is_apart_of(): void
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);

        /** @var GroupRepository $groupNameRepository */
        $groupNameRepository = $this->entityManager->getRepository(Group::class);
        /** @var Group $groupsUserIsApartOf */
        $groupsUserIsApartOf = $groupNameRepository->findGroupsUserIsApartOf($user);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);
        if (empty($devices)) {
            self::fail('no device found for test');
        }

        $newDeviceName = 'newDeviceName';
        $newPassword = 'NewPassword';

        $device = $devices[0];

        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => $newPassword,
            'deviceGroup' => $user->getGroup()->getGroupID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);
        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);
        self::assertEquals($user->getGroup()->getGroupID(), $responseData['payload']['group']['groupID']);
        self::assertEquals($user->getGroup()->getGroupName(), $responseData['payload']['group']['groupName']);
        self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
        self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
        self::assertEquals($newPassword, $responseData['payload']['secret']);

        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    /**
     * @dataProvider invalidDeviceUpdateRequestDataProvider
     */
    public function test_sending_out_of_range_device_update(string $deviceName, array $errorMessage): void
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(
            ['deviceName' => ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']]
        );

        $requestData = [
            'deviceName' => $deviceName,
            'password' => 'NewPassword',
            'deviceGroup' => $user->getGroup()->getGroupID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals($errorMessage, $responseData['errors']);
        self::assertEquals(APIErrorMessages::VALIDATION_ERRORS, $responseData['title']);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function invalidDeviceUpdateRequestDataProvider(): Generator
    {
        yield [
            'deviceName' => 'newDeviceNamenewDeviceNamenewDeviceNamenewDeviceNamenewDeviceNamenewDeviceName',
            'errorMessage' => [
                'Device name cannot be longer than 50 characters'
            ],
        ];

        yield [
            'deviceName' => 'x',
            'errorMessage' => [
                'Device name must be at least 2 characters long'
            ],
        ];
    }

    public function test_updating_device_correctly_admin(): void
    {
        $groupNameMappingRepository = $this->entityManager->getRepository(Group::class);

        /** @var GroupMapping[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $groupNameMappingRepository->findGroupsUserIsApartOf($this->adminUser);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);

        if (empty($devices)) {
            self::fail('no device found for test');
        }

        $device = $devices[0];

        /** @var Room[] $userRooms */
        $userRooms = $this->entityManager->getRepository(Room::class)->findAll();
        foreach ($userRooms as $userRoom) {
            if ($userRoom->getRoomID() !== $device->getRoomObject()->getRoomID()) {
                $newRoomID = $userRoom->getRoomID();
                break;
            }
        }

        foreach ($this->adminUser->getAssociatedGroupIDs() as $groupID) {
            if ($groupID !== $device->getGroupObject()->getGroupID()) {
                $newGroupID = $groupID;
                break;
            }
        }

        if (!isset($newRoomID)) {
            self::fail('no new room found for test');
        }

        $newDeviceName = 'newDeviceName';
        $newPassword = 'NewPassword';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => $newPassword,
            'deviceGroup' => $newGroupID ?? $device->getGroupObject()->getGroupID(),
            'deviceRoom' => $newRoomID,
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);
        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);
        self::assertEquals($newPassword, $responseData['payload']['secret']);

        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);
        self::assertEquals($newGroupID ?? $device->getGroupObject()->getGroupID(), $responseData['payload']['group']['groupID']);
        self::assertEquals($newRoomID, $responseData['payload']['room']['roomID']);
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    public function test_updating_device_correctly_regular_user(): void
    {
        /** @var User $user */
        $user = $this->regularUserTwo;
        $groupNameRepository = $this->entityManager->getRepository(Group::class);

        /** @var Group[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $groupNameRepository->findGroupsUserIsApartOf($user);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);

        if (empty($devices)) {
            self::fail('no device found for test');
        }

        $device = $devices[0];

        $userToken = $this->setUserToken(
            $this->client,
            $user->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );

        /** @var Room[] $userRooms */
        $userRooms = $this->entityManager->getRepository(Room::class)->findAll();
        foreach ($userRooms as $userRoom) {
            if ($userRoom->getRoomID() !== $device->getRoomObject()->getRoomID()) {
                $newRoomID = $userRoom->getRoomID();
                break;
            }
        }

        foreach ($user->getAssociatedGroupIDs() as $groupNameId) {
            if ($groupNameId !== $device->getGroupObject()->getGroupID()) {
                $newGroupNameID = $groupNameId;
                break;
            }
        }

        if (!isset($newRoomID)) {
            self::fail('no new room found for test');
        }

        $newDeviceName = 'newDeviceName';
        $newPassword = 'NewPassword';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => $newPassword,
            'deviceGroup' => $newGroupNameID ?? $device->getGroupObject()->getGroupID(),
            'deviceRoom' => $newRoomID,
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            $jsonPayload
        );
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);
        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);
        self::assertEquals($newPassword, $responseData['payload']['secret']);

        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);
        self::assertEquals($newGroupNameID ?? $device->getGroupObject()->getGroupID(), $responseData['payload']['group']['groupID']);
        self::assertEquals($newRoomID, $responseData['payload']['room']['roomID']);

    }

    public function test_device_with_password_updated_can_login(): void
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]
        );

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findAll();

        if (empty($devices)) {
            self::fail('no device found for test');
        }

        $device = $devices[0];

        $newDeviceName = 'NewDeviceName';
        $newPassword = 'NewDevicePassword';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => $newPassword,
            'deviceGroup' => $user->getGroup()->getGroupID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );

        if ($this->client->getResponse()->getStatusCode() === Response::HTTP_ACCEPTED) {
            $this->client->request(
                Request::METHOD_POST,
                SecurityController::API_DEVICE_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"' . $newDeviceName . '","password":"' . $newPassword .'","ipAddress":"192.168.1.2"}'
            );
            self::assertResponseStatusCodeSame(Response::HTTP_OK);

            $responseData = json_decode(
                $this->client->getResponse()->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            self::assertArrayHasKey('token', $responseData);
            self::assertArrayHasKey('refreshToken', $responseData);
        } else {
            self::fail('failed to get success response from device update');
        }

    }

    /**
     * @dataProvider sendingPatchRequestDataProvider
     */
    public function test_sending_successful_patch_request_admin(string $patchSubject): void
    {
        $user = $this->adminUser;

        /** @var GroupRepository $groupNameRepository */
        $groupNameRepository = $this->entityManager->getRepository(Group::class);

        /** @var Group[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupIDs(),
        );

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);

        if (empty($devices)) {
            self::fail('no device found for test');
        }

        $device = $devices[0];
        $rooms = $this->entityManager->getRepository(Room::class)->findAll();

        foreach ($groupsUserIsApartOf as $group) {
            if ($device->getGroupObject()->getGroupID() !== $group->getGroupID()) {
                $newGroupNameID = $group->getGroupID();
                break;
            }
        }

        foreach ($rooms as $room) {
            if ($device->getRoomObject()->getRoomID() !== $room->getRoomID()) {
                $newRoomID = $room->getRoomID();
                break;
            }
        }

        $requestData = match ($patchSubject) {
            'deviceName' => [
                'deviceName' => 'NewDeviceName',
            ],
            'password' => [
                'password' => 'NewDevicePassword',
            ],
            'deviceGroup' => [
                'deviceGroup' => $newGroupNameID,
            ],
            'deviceRoom' => [
                'deviceRoom' => $newRoomID,
            ],
            default => self::fail('unknown patch subject'),
        };

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);

        switch ($patchSubject) {
            case 'deviceName':
                self::assertEquals($requestData['deviceName'], $responseData['payload']['deviceName']);
                self::assertEquals($device->getGroupObject()->getGroupID(), $responseData['payload']['group']['groupID']);
                self::assertEquals($device->getGroupObject()->getGroupName(), $responseData['payload']['group']['groupName']);
                self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
                break;
            case 'password':
                self::assertEquals($device->getGroupObject()->getGroupID(), $responseData['payload']['group']['groupID']);
                self::assertEquals($device->getGroupObject()->getGroupName(), $responseData['payload']['group']['groupName']);
                self::assertEquals($requestData['password'], $responseData['payload']['secret']);
                self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
                break;
            case 'deviceGroup':
                self::assertEquals($newGroupNameID, $responseData['payload']['group']['groupID']);
                self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
                self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
                break;
            case 'deviceRoom':
                self::assertEquals($newRoomID, $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
                self::assertEquals($device->getGroupObject()->getGroupID(), $responseData['payload']['group']['groupID']);
                self::assertEquals($device->getGroupObject()->getGroupName(), $responseData['payload']['group']['groupName']);
                break;
        }
        self::assertEquals($device->getDeviceID(), $responseData['payload']['deviceNameID']);
        self::assertEquals(Devices::ROLE, $responseData['payload']['roles'][0]);
    }

    /**
     * @dataProvider sendingPatchRequestDataProvider
     */
    public function test_sending_successful_patch_request_regular_user(string $patchSubject): void
    {
        $user = $this->regularUserTwo;

        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_PASSWORD,
        );

        /** @var GroupRepository $groupNameMappingRepository */
        $groupNameMappingRepository = $this->entityManager->getRepository(Group::class);

        /** @var Group[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $groupNameMappingRepository->findGroupsUserIsApartOf($user);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);

        $rooms = $this->entityManager->getRepository(Room::class)->findAll();
        if (empty($devices)) {
            self::fail('no device found for test');
        }

        $device = $devices[0];

        foreach ($groupsUserIsApartOf as $group) {
            if ($device->getGroupObject()->getGroupID() !== $group->getGroupID()) {
                $newGroupNameID = $group->getGroupID();
                break;
            }
        }

        foreach ($rooms as $room) {
            if ($device->getRoomObject()->getRoomID() !== $room->getRoomID()) {
                $newRoomID = $room->getRoomID();
                break;
            }
        }

        $requestData = match ($patchSubject) {
            'deviceName' => [
                'deviceName' => 'NewDeviceName',
            ],
            'password' => [
                'password' => 'NewDevicePassword',
            ],
            'deviceGroup' => [
                'deviceGroup' => $newGroupNameID,
            ],
            'deviceRoom' => [
                'deviceRoom' => $newRoomID,
            ],
            default => self::fail('unknown patch subject'),
        };

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            $jsonPayload
        );
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);

        switch ($patchSubject) {
            case 'deviceName':
                self::assertEquals($requestData['deviceName'], $responseData['payload']['deviceName']);
                self::assertEquals($device->getGroupObject()->getGroupID(), $responseData['payload']['group']['groupID']);
                self::assertEquals($device->getGroupObject()->getGroupName(), $responseData['payload']['group']['groupName']);
                self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
                break;
            case 'password':
                self::assertEquals($requestData['password'], $responseData['payload']['secret']);
                self::assertEquals($device->getGroupObject()->getGroupID(), $responseData['payload']['group']['groupID']);
                self::assertEquals($device->getGroupObject()->getGroupName(), $responseData['payload']['group']['groupName']);
                self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
                break;
            case 'deviceGroup':
                self::assertEquals($requestData['deviceGroup'], $responseData['payload']['group']['groupID']);
                self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
                self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
                break;
            case 'deviceRoom':
                self::assertEquals($requestData['deviceRoom'], $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
                self::assertEquals($device->getGroupObject()->getGroupID(), $responseData['payload']['group']['groupID']);
                self::assertEquals($device->getGroupObject()->getGroupName(), $responseData['payload']['group']['groupName']);
                break;
        }
        self::assertEquals($device->getDeviceID(), $responseData['payload']['deviceNameID']);
        self::assertEquals(Devices::ROLE, $responseData['payload']['roles'][0]);
    }

    public function sendingPatchRequestDataProvider(): Generator
    {
        yield [
            'deviceName',
        ];

        yield [
            'password',
        ];

        yield [
            'deviceGroup',
        ];

        yield [
            'deviceRoom',
        ];
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            sprintf(self::UPDATE_DEVICE_URL, 1),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_DELETE],
            [Request::METHOD_POST],
        ];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
