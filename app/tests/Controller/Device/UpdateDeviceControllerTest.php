<?php

namespace App\Tests\Controller\Device;

use App\Controller\Authentication\SecurityController;
use App\Controller\Device\UpdateDeviceController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Authentication\GroupMapping;
use App\Entity\Device\Devices;
use App\Entity\Sensor\Sensor;
use App\Entity\User\Group;
use App\Entity\User\Room;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Repository\User\ORM\GroupRepository;
use App\Services\API\APIErrorMessages;
use App\Services\API\HTTPStatusCodes;
use App\Services\Request\RequestTypeEnum;
use App\Tests\Controller\ControllerTestCase;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateDeviceControllerTest extends ControllerTestCase
{
    private const UPDATE_DEVICE_URL = '/HomeApp/api/user/user-devices/%d?responseType=%s';

    private DeviceRepositoryInterface $deviceRepository;

    private SensorRepositoryInterface $sensorRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
    }

    public function test_sending_wrong_encoding_request(): void
    {
        /** @var Devices $device */
        $device = $this->deviceRepository->findBy(['groupID' => $this->adminOne->getGroup()])[0];

        $requestData = [
            'deviceName' => '$deviceName',
            'password' => '$password',
            'deviceGroup' => '$deviceGroup',
            'deviceRoom' => '$deviceRoom',
        ];

        $this->authenticateAdminOne();
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            [],
            [],
            [],
            implode(',', $requestData)
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
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
        $device = $this->deviceRepository->findBy(['groupID' => $this->adminOne->getGroup()])[0];

        $requestData = [
            'deviceName' => $deviceName,
            'password' => $password,
            'deviceGroup' => $deviceGroup,
            'deviceRoom' => $deviceRoom,
        ];

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData
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
                'deviceName' => 'This value should be of type string.'
            ],
        ];

        yield [
            'deviceName' => 'newDeviceName',
            'password' => ['NewPassword'],
            'deviceGroup' => 1,
            'deviceRoom' => 1,
            'errorMessage' => [
                'password' => 'This value should be of type string.'
            ],
        ];

        yield [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => 'deviceGroup',
            'deviceRoom' => 1,
            'errorMessage' => [
                'deviceGroup' => 'This value should be of type int.'
            ],
        ];

        yield [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => 1,
            'deviceRoom' => 'deviceRoom',
            'errorMessage' => [
                'deviceRoom' => 'This value should be of type int.'
            ],
        ];

        yield [
            'deviceName' => 1,
            'password' => ['NewPassword'],
            'deviceGroup' => [1],
            'deviceRoom' => 'deviceRoom',
            'errorMessage' => [
                'deviceName' => 'This value should be of type string.',
                'deviceGroup' => 'This value should be of type int.',
                'deviceRoom' => 'This value should be of type int.',
                'password' => 'This value should be of type string.',

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

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $nonExistentDeviceID, RequestTypeEnum::SENSITIVE_FULL->value),
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
        $device = $this->deviceRepository->findBy(['groupID' => $this->adminOne->getGroup()])[0];

        if ($device === null) {
            self::fail('no device found for test');
        }

        $requestData = [
            'deviceName' => 'newDeviceName',
            'deviceGroup' => $this->adminOne->getGroup()->getGroupID(),
            'deviceRoom' => $nonExistentRoomID,
        ];


        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData
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
        $device = $this->deviceRepository->findBy(['groupID' => $this->adminOne->getGroup()->getGroupID()])[0];

        $requestData = [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => $nonExistentGroupID,
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData,
        );
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertEquals(['Group not found for id ' . $nonExistentGroupID], $responseData['errors']);
        self::assertEquals(UpdateDeviceController::NOTHING_FOUND, $responseData['title']);
    }

    public function test_regular_user_cannot_update_device_group_not_apart_of(): void
    {

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


        $this->authenticateRegularUserTwo();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData
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
            $this->adminOne,
        );

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);
        if (empty($devices)) {
            self::fail('no device found for test');
        }

        foreach ($devices as $deviceToTest) {
            $sensors = $this->sensorRepository->findBy(['deviceID' => $deviceToTest->getDeviceID()]);
            if (!empty($sensors)) {
                $device = $deviceToTest;
            }
        }
        if (!isset($device)) {
            self::fail('No device with sensors found');
        }

        $newDeviceName = 'newDeviceName';
        $newPassword = 'NewPassword';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => $newPassword,
            'deviceGroup' => $this->adminOne->getGroup()->getGroupID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $payload = $responseData['payload'];

        $device = $this->entityManager->getRepository(Devices::class)->find($device->getDeviceID());
        self::assertEquals('Device Successfully Updated', $responseData['title']);

        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);
        self::assertDeviceIsSameAsExpected($device, $payload);
        self::assertEquals($newPassword, $payload['secret']);
        self::assertTrue($payload['canEdit']);
        self::assertTrue($payload['canDelete']);

        self::assertArrayHasKey('sensorData', $payload);
        $sensorData = $payload['sensorData'];

        foreach ($sensorData as $data) {
            self::assertArrayHasKey('sensorID', $data);
            self::assertArrayHasKey('sensorName', $data);
            self::assertArrayHasKey('createdBy', $data);
            self::assertArrayHasKey('device', $data);
            self::assertArrayHasKey('sensorType', $data);
            self::assertArrayHasKey('sensorReadingTypes', $data);
        }
    }

    public function test_admin_can_update_device_is_apart_of(): void
    {
        /** @var GroupRepository $groupNameRepository */
        $groupNameRepository = $this->entityManager->getRepository(Group::class);
        /** @var Group $groupsUserIsApartOf */
        $groupsUserIsApartOf = $groupNameRepository->findGroupsUserIsApartOf($this->adminTwo);

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
            'deviceGroup' => $this->adminTwo->getGroup()->getGroupID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
            'responseType' => RequestTypeEnum::SENSITIVE_FULL,
        ];

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);
        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);
        self::assertGroupIsSamAsExpected($this->adminTwo->getGroup(), $responseData['payload']['group']);;
        self::assertEquals($newPassword, $responseData['payload']['secret']);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @dataProvider invalidDeviceUpdateRequestDataProvider
     */
    public function test_sending_out_of_range_device_update(string $deviceName, array $errorMessage): void
    {
        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(
            ['deviceName' => ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']]
        );

        $requestData = [
            'deviceName' => $deviceName,
            'password' => 'NewPassword',
            'deviceGroup' => $this->adminTwo->getGroup()->getGroupID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
            'responseType' => RequestTypeEnum::SENSITIVE_FULL,
        ];

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData
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
                'deviceName' => 'Device name cannot be longer than 50 characters'
            ],
        ];

        yield [
            'deviceName' => 'x',
            'errorMessage' => [
                'deviceName' => 'Device name must be at least 2 characters long'
            ],
        ];
    }

    public function test_updating_device_correctly_admin(): void
    {
        $groupNameMappingRepository = $this->entityManager->getRepository(Group::class);

        /** @var GroupMapping[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $groupNameMappingRepository->findGroupsUserIsApartOf($this->adminOne);

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

        foreach ($this->adminOne->getAssociatedGroupIDs() as $groupID) {
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

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);
        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);

        self::assertEquals($newGroupID ?? $device->getGroupObject()->getGroupID(), $responseData['payload']['group']['groupID']);
        self::assertEquals($newRoomID, $responseData['payload']['room']['roomID']);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_updating_device_correctly_regular_user(): void
    {
        $groupNameRepository = $this->entityManager->getRepository(Group::class);

        /** @var Group[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $groupNameRepository->findGroupsUserIsApartOf($this->regularUserTwo);

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

        foreach ($this->regularUserTwo->getAssociatedGroupIDs() as $groupNameId) {
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


        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData,
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);
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

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData,
        );

        if ($this->client->getResponse()->getStatusCode() === Response::HTTP_OK) {
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
        $user = $this->adminOne;

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

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

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
        self::assertEquals($device->getDeviceID(), $responseData['payload']['deviceID']);
        self::assertEquals(Devices::ROLE, $responseData['payload']['roles'][0]);
    }

    /**
     * @dataProvider sendingPatchRequestDataProvider
     */
    public function test_sending_successful_patch_request_regular_user(string $patchSubject): void
    {
        $user = $this->regularUserTwo;

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

        $this->authenticateRegularUserTwo();
        $this->client->jsonRequest(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceID(), RequestTypeEnum::SENSITIVE_FULL->value),
            $requestData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

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
        self::assertEquals($device->getDeviceID(), $responseData['payload']['deviceID']);
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

//    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_using_wrong_http_method(string $httpVerb): void
//    {
//        $this->client->request(
//            $httpVerb,
//            sprintf(self::UPDATE_DEVICE_URL, 1),
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
//        );
//
//        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
//    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_DELETE],
            [Request::METHOD_POST],
        ];
    }
}
