<?php

namespace App\Tests\Controller\Device;

use App\DataFixtures\Core\RoomFixtures;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Authentication\GroupMapping;
use App\Entity\Common\IPLog;
use App\Entity\Device\Devices;
use App\Entity\User\Group;
use App\Entity\User\Room;
use App\Entity\User\User;
use App\Repository\Authentication\ORM\GroupMappingRepository;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\HTTPStatusCodes;
use App\Services\Request\RequestTypeEnum;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddNewDeviceControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_NEW_DEVICE_PATH = '/HomeApp/api/user/user-devices';

    private const UNIQUE_NEW_DEVICE_NAME = 'newDeviceName';

    private const NEW_DEVICE_PASSWORD = 'Test1234';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private Group $groupName;

    private Room $room;

    private DeviceRepositoryInterface $deviceRepository;

    private User $regularUserTwo;

    private User $adminUser;

    private GroupRepositoryInterface $groupNameRepository;

    private GroupMappingRepository $groupNameMappingRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $this->adminUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);

        $this->groupNameRepository = $this->entityManager->getRepository(Group::class);
        $this->groupNameMappingRepository = $this->entityManager->getRepository(GroupMapping::class);

        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->groupName = $this->entityManager->getRepository(Group::class)->findOneByName(UserDataFixtures::ADMIN_GROUP_ONE);
        $this->room = $this->entityManager->getRepository(Room::class)->findRoomByName(RoomFixtures::LIVING_ROOM);
        $this->userToken = $this->setUserToken($this->client);
    }

    public function test_sending_wrong_encoding_request(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );
        self::assertEquals(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $this->client->getResponse()->getStatusCode());
    }

    public function test_add_new_device_admin_sensitive_response(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
            'responseType' => RequestTypeEnum::SENSITIVE_FULL->value,
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_CREATED);

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => self::UNIQUE_NEW_DEVICE_NAME]);
        self::assertNotNull($device);

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $payload = $responseData['payload'];

        self::assertNotNull($payload['deviceID']);
        self::assertNull($payload['ipAddress']);
        self::assertNull($payload['externalIpAddress']);
        self::assertEquals(self::UNIQUE_NEW_DEVICE_NAME, $payload['deviceName']);
        self::assertEquals(Devices::ROLE, $payload['roles'][0]);
        self::assertEquals(self::NEW_DEVICE_PASSWORD, $payload['secret']);
        self::assertTrue($payload['canEdit']);
        self::assertTrue($payload['canDelete']);
    }

    public function test_add_new_device_regular_user_sensitive_response(): void
    {
        $deviceIPAddress = '192.168.1.114';
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->regularUserTwo->getGroup()->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
            'deviceIPAddress' => $deviceIPAddress,
            'responseType' => RequestTypeEnum::SENSITIVE_FULL->value,
        ];

        $userToken = $this->setUserToken(
            $this->client,
            $this->regularUserTwo->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$userToken],
        );
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_CREATED);

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => self::UNIQUE_NEW_DEVICE_NAME]);
        self::assertNotNull($device);

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $payload = $responseData['payload'];

        self::assertNotNull($payload['deviceID']);
        self::assertNull($payload['externalIpAddress']);
        self::assertEquals(self::UNIQUE_NEW_DEVICE_NAME, $payload['deviceName']);
        self::assertEquals(Devices::ROLE, $payload['roles'][0]);
        self::assertEquals(self::NEW_DEVICE_PASSWORD, $payload['secret']);
        self::assertTrue($payload['canEdit']);
        self::assertTrue($payload['canDelete']);
        self::assertEquals($deviceIPAddress, $payload['ipAddress']);
    }

    public function test_adding_device_with_ip_removes_ip_from_log_table(): void
    {
        $ipLogRepository = $this->entityManager->getRepository(IPLog::class);
        /** @var IPLog[] $allIPLogs */
        $allIPLogs = $ipLogRepository->findAll();

        $ipLog = $allIPLogs[0];
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->regularUserTwo->getGroup()->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
            'deviceIPAddress' => $ipLog->getIpAddress(),
        ];

        $userToken = $this->setUserToken(
            $this->client,
            $this->regularUserTwo->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$userToken],
        );
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_CREATED);

        $ipRemovedCheck = $ipLogRepository->findOneBy(['ipAddress' => $ipLog->getIpAddress()]);
        self::assertNull($ipRemovedCheck);
    }

    public function test_add_duplicate_device_name_same_room(): void
    {
        $formData = [
            'deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name'],
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString(sprintf(
            'Your group already has a device named %s that is in room %s',
            ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name'],
            $this->room->getRoom(),
        ), $responseData['errors'][0]);
    }

    public function test_sending_malformed_request_missing_name(): void
    {
        $formData = [
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertStringContainsString('Device name is a required field', $responseData['errors']['deviceName']);
    }

    public function test_sending_malformed_request_missing_group(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($device);
        self::assertStringContainsString('Device group is a required field', $responseData['errors']['deviceGroup']);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malformed_request_missing_room(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($device);
        self::assertStringContainsString('Device room is a required field', $responseData['errors']['deviceRoom']);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malformed_request_missing_password(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($device);
        self::assertStringContainsString('Device password is a required field', $responseData['errors']['devicePassword']);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }


    /**
     * @dataProvider addingDeviceSendingMalformedRequestDataProvider
     */
    public function test_adding_device_sending_malformed_request(array $formData, array $errors): void
    {
        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);

        self::assertNull($device);
        self::assertEquals($errors, $responseData['errors']);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function addingDeviceSendingMalformedRequestDataProvider(): Generator
    {
        yield [
            'formData' => [
                'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
                'devicePassword' => self::NEW_DEVICE_PASSWORD,
                'deviceGroup' => 'string',
                'deviceRoom' => 1,
            ],
            'errorMessage' => [
                'deviceGroup' => 'This value should be of type int.'
            ],
        ];

        yield [
            'formData' => [
                'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
                'devicePassword' => self::NEW_DEVICE_PASSWORD,
                'deviceGroup' => 2,
                'deviceRoom' => 'string',
            ],
            'errorMessage' => [
                'deviceRoom' => 'This value should be of type int.'
            ],
        ];

        yield [
            'formData' => [
                'deviceName' => 'randon_name',
                'devicePassword' => 'random_password',
                'deviceGroup' => 1,
                'deviceRoom' => 1,
                'deviceIPAddress' => [],
            ],
            'errorMessage' => [
                'deviceIPAddress' => 'This value should be of type string.',
            ],
        ];

        yield [
            'formData' => [
                'deviceName' => ['dfg'],
                'devicePassword' => ['dfg'],
                'deviceGroup' => ['dfg'],
                'deviceRoom' => ['dfg'],
                'deviceIPAddress' => ['dfg'],
            ],
            'errorMessage' => [
                'deviceName' => 'This value should be of type string.',
                'devicePassword' => 'This value should be of type string.',
                'deviceGroup' => 'This value should be of type int.',
                'deviceRoom' => 'This value should be of type int.',
                'deviceIPAddress' => 'This value should be of type string.',
            ],
        ];
    }

    public function test_adding_device_name_too_long(): void
    {
        $formData = [
            'deviceName' => 'thisNameIsWaaaaaaaayTooooLoooongthisNameIsWaaaaaaaayTooooLoooong',
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNull($device);
        self::assertStringContainsString('Device name cannot be longer than 50 characters', $responseData['errors']['deviceName']);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_name_special_characters(): void
    {
        $formData = [
            'deviceName' => 'device&&**name',
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNull($device);
        self::assertStringContainsString('The name cannot contain any special characters, please choose a different name', $responseData['errors']['deviceName']);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_password_name_too_short(): void
    {
        $formData = [
            'deviceName' => 'devicename',
            'devicePassword' => '1',
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNull($device);
        self::assertStringContainsString('Device password must be at least 5 characters long', $responseData['errors']['devicePassword']);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_password_name_too_long(): void
    {
        $formData = [
            'deviceName' => 'devicename',
            'devicePassword' => 'devicePasswordIsWayTooLong1111111111111devicePasswordIsWayTooLong1111111111111devicePasswordIsWayTooLong1111111111111',
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNull($device);
        self::assertStringContainsString('Device password cannot be longer than 100 characters', $responseData['errors']['devicePassword']);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_to_group_not_apart_of(): void
    {
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        /** @var Group $groupUserIsNotApartOf */
        $groupUserIsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupIDs(),
        )[0];

        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $groupUserIsNotApartOf->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNull($device);
        self::assertEquals(HTTPStatusCodes::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_unrecognised_room(): void
    {
        $roomRepository = $this->entityManager->getRepository(Room::class);
        while (true) {
            $noneExistentRoomID = random_int(1, 10000);
            $room = $roomRepository->find($noneExistentRoomID);

            if ($room === null) {
                break;
            }
        }

        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $noneExistentRoomID,
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals('Nothing Found', $responseData['title']);
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('Room not found for id ' . $noneExistentRoomID, $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertNull($device);
    }

    public function test_adding_device_unrecognised_group(): void
    {
        $groupRepository = $this->entityManager->getRepository(Group::class);
        while (true) {
            $noneExistentGroupID = random_int(1, 10000);
            $group = $groupRepository->find($noneExistentGroupID);

            if ($group === null) {
                break;
            }
        }

        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $noneExistentGroupID,
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $device = $this->deviceRepository->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals('Nothing Found', $responseData['title']);
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('Group not found for id ' . $noneExistentGroupID, $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertNull($device);
    }

    public function test_cannot_add_device_with_no_token(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertStringContainsString('JWT Token not found', $responseData['message']);
        self::assertEquals(HTTPStatusCodes::HTTP_UNAUTHORISED, $this->client->getResponse()->getStatusCode());
    }

//    public function test_device_password_is_correct_format(): void
//    {
//        $formData = [
//            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
//            'deviceGroup' => $this->groupName->getgroupID(),
//            'deviceRoom' => $this->room->getRoomID(),
//        ];
//
//        $jsonData = json_encode($formData);
//
//        $this->client->request(
//            Request::METHOD_POST,
//            self::ADD_NEW_DEVICE_PATH,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//            $jsonData,
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['payload'];
//
//        self::assertMatchesRegularExpression('/^[a-f\d]{32}$/', $responseData['secret']);
//    }

    public function test_new_device_can_login(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupID(),
            'deviceRoom' => $this->room->getRoomID(),
            'responseType' => RequestTypeEnum::SENSITIVE_FULL->value,
        ];

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['payload'];

        $createResponseCode = $this->client->getResponse()->getStatusCode();
        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $createResponseCode);

        $loginFormData = [
            'username' => $responseData['deviceName'],
            'password' => $responseData['secret'],
        ];

        $loginJsonData = json_encode($loginFormData);

        $this->client->request(
            Request::METHOD_POST,
            \App\Controller\Authentication\SecurityController::API_DEVICE_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $loginJsonData,
        );
        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $loginResponseData = json_decode($this->client->getResponse()->getContent(), true, 512);

        if (empty($loginResponseData['token'])) {
            self::fail('failed to get token from login after adding new device method: test_new_device_can_login');
        }
        self::assertArrayHasKey('token', $loginResponseData);
        self::assertArrayHasKey('refreshToken', $loginResponseData);
    }

    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_adding_device_wrong_http_method(string $httpVerb): void
//    {
//        $this->client->request(
//            $httpVerb,
//            self::ADD_NEW_DEVICE_PATH,
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
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }
}
