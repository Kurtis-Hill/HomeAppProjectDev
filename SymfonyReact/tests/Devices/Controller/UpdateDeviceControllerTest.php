<?php

namespace App\Tests\Devices\Controller;

use App\Common\API\HTTPStatusCodes;
use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Doctrine\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Authentication\Controller\SecurityController;
use App\Authentication\Entity\GroupNameMapping;
use App\Common\API\APIErrorMessages;
use App\Devices\Entity\Devices;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupNameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateDeviceControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const UPDATE_DEVICE_URL = '/HomeApp/api/user/user-devices/update-device/%d';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken($this->client);
    }

    public function test_sending_wrong_encoding_request(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $user->getGroupNameID()])[0];

        $requestData = [
            'deviceName' => '$deviceName',
            'password' => '$password',
            'deviceGroup' => '$deviceGroup',
            'deviceRoom' => '$deviceRoom',
        ];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
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
     * @dataProvider sendingWrongTypesToUpdateDataProvider
     */
    public function test_sending_wrong_types_to_update(
        mixed $deviceName,
        mixed $password,
        mixed $deviceGroup,
        mixed $deviceRoom,
        array $errorMessage,
    ): void {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $user->getGroupNameID()])[0];

        $requestData = [
            'deviceName' => $deviceName,
            'password' => $password,
            'deviceGroup' => $deviceGroup,
            'deviceRoom' => $deviceRoom,
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
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

    public function sendingWrongTypesToUpdateDataProvider(): Generator
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

    public function testSendingDeviceThatDoesntExist(): void
    {
        $deviceRepository = $this->entityManager->getRepository(Devices::class);
        while (true) {
            $nonExistentDeviceID = random_int(1, 100000);

            $device = $deviceRepository->findOneBy(['deviceNameID' => $nonExistentDeviceID]);
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

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testSendingNoneExistentRoomID(): void
    {
        $roomRepository = $this->entityManager->getRepository(Room::class);
        while (true) {
            $nonExistentRoomID = random_int(1, 100000);

            $room = $roomRepository->findOneBy(['roomID' => $nonExistentRoomID]);
            if (!$room instanceof Room) {
                break;
            }
        }
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $room = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $user->getGroupNameID()])[0];


        $requestData = [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => $user->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $nonExistentRoomID,
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $room->getDeviceNameID()),
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
        self::assertEquals(['The id provided for room doesnt match any room we have'], $responseData['errors']);
        self::assertEquals('Room not found', $responseData['title']);
    }

    public function testSendingNoneExistentGroupID(): void
    {
        $groupRepository = $this->entityManager->getRepository(GroupNames::class);
        while (true) {
            $nonExistentGroupID = random_int(1, 100000);

            $group = $groupRepository->findOneBy(['groupNameID' => $nonExistentGroupID]);
            if (!$group instanceof Room) {
                break;
            }
        }
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $user->getGroupNameID()->getGroupNameID()])[0];

        $requestData = [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => $nonExistentGroupID,
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
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
        self::assertEquals(['The id provided for groupname doesnt match any groupname we have'], $responseData['errors']);
        self::assertEquals('Group name not found', $responseData['title']);
    }

    public function testRegularUserCannotUpdateDeviceNotApartOf(): void
    {
        $userToken = $this->setUserToken($this->client, UserDataFixtures::SECOND_REGULAR_USER_ISOLATED, UserDataFixtures::REGULAR_PASSWORD);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER]);

        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);
        $groupUserIsNotApartOf = $groupNameMappingRepository->findGroupsUserIsNotApartOf($user->getGroupNameIds())[0];

        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsNotApartOf->getGroupNameID()])[0];

        $requestData = [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => $user->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
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

    public function testAdminCanUpdateDeviceNotApartOf(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::SECOND_ADMIN_USER_ISOLATED]);

        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);
        $groupUserIsNotApartOf = $groupNameMappingRepository->findGroupsUserIsNotApartOf($user->getGroupNameIds())[0];

        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsNotApartOf->getGroupNameID()])[0];

        $newDeviceName = 'newDeviceName';
        $newPassword = 'NewPassword';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => $newPassword,
            'deviceGroup' => $user->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
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
        self::assertEquals($user->getGroupNameID()->getGroupNameID(), $responseData['payload']['groupName']['groupNameID']);
        self::assertEquals($user->getGroupNameID()->getGroupName(), $responseData['payload']['groupName']['groupName']);
        self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
        self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
        self::assertEquals($newPassword, $responseData['payload']['secret']);

        self::assertEquals(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider sendingOutOfRangeDeviceUpdateDataProvider
     */
    public function testSendingOutOfRangeDeviceUpdate(string $deviceName, array $errorMessage): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['email' => UserDataFixtures::SECOND_ADMIN_USER_ISOLATED]
        );

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(
            ['deviceName' => ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']]
        );

        $requestData = [
            'deviceName' => $deviceName,
            'password' => 'NewPassword',
            'deviceGroup' => $user->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
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

    public function sendingOutOfRangeDeviceUpdateDataProvider(): Generator
    {
        yield [
            'deviceName' => 'newDeviceNamenewDeviceNamenewDeviceName',
            'errorMessage' => [
                'Device name cannot be longer than 20 characters'
            ],
        ];

        yield [
            'deviceName' => 'x',
            'errorMessage' => [
                'Device name must be at least 2 characters long'
            ],
        ];
    }

    public function testUpdatingDeviceCorrectly(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);
        $groupUserIsApartOf = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user)[0];

        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsApartOf->getGroupNameID()])[0];

        $userRooms = $this->entityManager->getRepository(Room::class)->getAllUserRoomsByGroupId($user->getGroupNameIds());
        foreach ($userRooms as $userRoom) {
            if ($userRoom['roomID'] !== $device->getRoomObject()->getRoomID()) {
                $newRoomID = $userRoom['roomID'];
                break;
            }
        }

        foreach ($user->getGroupNameIds() as $groupNameId) {
            if ($groupNameId !== $device->getGroupNameObject()->getGroupNameID()) {
                $newGroupNameID = $groupNameId;
                break;
            }
        }

        $newDeviceName = 'newDeviceName';
        $newPassword = 'NewPassword';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => $newPassword,
            'deviceGroup' => $newGroupNameID,
            'deviceRoom' => $newRoomID,
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
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
        self::assertEquals($newGroupNameID, $responseData['payload']['groupName']['groupNameID']);
        self::assertEquals($newRoomID, $responseData['payload']['room']['roomID']);
        self::assertEquals(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());
    }

    public function testDeviceWithPasswordUpdatedCanLogIn(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['email' => UserDataFixtures::SECOND_ADMIN_USER_ISOLATED]
        );

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(
            ['deviceName' => ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']]
        );

        $newDeviceName = 'NewDeviceName';
        $newPassword = 'NewDevicePassword';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => $newPassword,
            'deviceGroup' => $user->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
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

            $responseData = json_decode(
                $this->client->getResponse()->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            self::assertArrayHasKey('token', $responseData);
            self::assertArrayHasKey('refreshToken', $responseData);
            self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        } else {
            self::fail('failed to get success response from device update');
        }
    }

    /**
     * @dataProvider sendingPatchRequestDataProvider
     */
    public function testSendingPatchRequest(string $patchSubject): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);
        $groupUserIsApartOf = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user)[0];

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsApartOf->getGroupNameID()])[0];

        $requestData = match ($patchSubject) {
            'deviceName' => [
                'deviceName' => 'NewDeviceName',
            ],
            'password' => [
                'password' => 'NewDevicePassword',
            ],
            'deviceGroup' => [
                'deviceGroup' => $groupUserIsApartOf->getGroupNameID()->getGroupNameID(),
            ],
            'deviceRoom' => [
                'deviceRoom' => $device->getRoomObject()->getRoomID(),
            ],
            default => self::fail('unknown patch subject'),
        };

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
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
        self::assertEquals(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());

        switch ($patchSubject) {
            case 'deviceName':
                self::assertEquals($requestData['deviceName'], $responseData['payload']['deviceName']);
                self::assertEquals($device->getDeviceNameID(), $responseData['payload']['deviceNameID']);
                self::assertEquals($device->getGroupNameObject()->getGroupNameID(), $responseData['payload']['groupName']['groupNameID']);
                self::assertEquals($device->getGroupNameObject()->getGroupName(), $responseData['payload']['groupName']['groupName']);
                self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
                self::assertEquals(Devices::ROLE, $responseData['payload']['roles'][0]);
                self::assertNull($responseData['payload']['secret']);
                break;
            case 'password':
                self::assertEquals($requestData['password'], $responseData['payload']['secret']);
                break;
            case 'deviceGroup':
                self::assertEquals($requestData['deviceGroup'], $responseData['payload']['groupName']['groupNameID']);
                self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
                self::assertEquals($device->getDeviceNameID(), $responseData['payload']['deviceNameID']);
                self::assertEquals($device->getRoomObject()->getRoomID(), $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getRoomObject()->getRoom(), $responseData['payload']['room']['roomName']);
                self::assertEquals(Devices::ROLE, $responseData['payload']['roles'][0]);
                self::assertNull($responseData['payload']['secret']);
                break;
            case 'deviceRoom':
                self::assertEquals($requestData['deviceRoom'], $responseData['payload']['room']['roomID']);
                self::assertEquals($device->getDeviceName(), $responseData['payload']['deviceName']);
                self::assertEquals($device->getDeviceNameID(), $responseData['payload']['deviceNameID']);
                self::assertEquals($device->getGroupNameObject()->getGroupNameID(), $responseData['payload']['groupName']['groupNameID']);
                self::assertEquals($device->getGroupNameObject()->getGroupName(), $responseData['payload']['groupName']['groupName']);
                self::assertEquals(Devices::ROLE, $responseData['payload']['roles'][0]);
                self::assertNull($responseData['payload']['secret']);
                break;
        }
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

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
