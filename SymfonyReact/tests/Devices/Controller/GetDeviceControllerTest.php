<?php

namespace App\Tests\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Devices\Controller\GetDeviceController;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupNameRepository;
use App\User\Repository\ORM\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetDeviceControllerTest extends WebTestCase
{
    use TestLoginTrait;
    private const GET_SINGLE_DEVICE_URL = CommonURL::USER_HOMEAPP_API_URL . 'user-device/%d';

    private const GET_ALL_DEVICES_URL = CommonURL::USER_HOMEAPP_API_URL . 'user-device/all';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private DeviceRepository $deviceRepository;

    private UserRepository $userRepository;

    private GroupNameRepository $groupNameRepository;

    //@TODO add tests for getting full device response
    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken($this->client);

        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
    }

    public function test_get_device_of_group_user_is_not_assigned_to_regular_user(): void
    {
        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $groupsUserIsNotAssignedTo = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupNameID' => $groupsUserIsNotAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }

        $device = $devices[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],

        );
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $errors = $responseData['errors'];
        $title = $responseData['title'];

        self::assertEquals('You Are Not Authorised To Be Here', $title);
        self::assertEquals(APIErrorMessages::ACCESS_DENIED, $errors[0]);
    }

    public function test_get_device_of_group_user_is_not_assigned_to_admin(): void
    {
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);

        $groupsUserIsNotAssignedTo = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupNameID' => $groupsUserIsNotAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],

        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);


        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
        self::assertEquals($device->getDeviceID(), $payload['deviceNameID']);
        self::assertEquals($device->getDeviceName(), $payload['deviceName']);
        self::assertNull($payload['secret']);
        self::assertEquals($device->getGroupNameObject()->getGroupName(), $payload['groupName']['groupName']);
        self::assertEquals($device->getGroupNameObject()->getGroupNameID(), $payload['groupName']['groupNameID']);
        self::assertEquals($device->getRoomObject()->getRoomID(), $payload['room']['roomID']);
        self::assertEquals($device->getRoomObject()->getRoom(), $payload['room']['roomName']);
        self::assertArrayHasKey('ipAddress', $payload);
        self::assertArrayHasKey('externalIpAddress', $payload);
        self::assertEquals($device->getRoles(), $payload['roles']);
    }

    public function test_get_device_of_group_user_is_assigned_to_regular_user(): void
    {
        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($user, $user->getAssociatedGroupNameIds());

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupNameID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],

        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
        self::assertEquals($device->getDeviceID(), $payload['deviceNameID']);
        self::assertEquals($device->getDeviceName(), $payload['deviceName']);
        self::assertNull($payload['secret']);
        self::assertEquals($device->getGroupNameObject()->getGroupName(), $payload['groupName']['groupName']);
        self::assertEquals($device->getGroupNameObject()->getGroupNameID(), $payload['groupName']['groupNameID']);
        self::assertEquals($device->getRoomObject()->getRoomID(), $payload['room']['roomID']);
        self::assertEquals($device->getRoomObject()->getRoom(), $payload['room']['roomName']);
        self::assertArrayHasKey('ipAddress', $payload);
        self::assertArrayHasKey('externalIpAddress', $payload);
        self::assertEquals($device->getRoles(), $payload['roles']);

    }

    public function test_get_device_of_group_user_is_assigned_to_admin(): void
    {
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);

        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($user, $user->getAssociatedGroupNameIds());

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupNameID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],

        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
        self::assertEquals($device->getDeviceID(), $payload['deviceNameID']);
        self::assertEquals($device->getDeviceName(), $payload['deviceName']);
        self::assertNull($payload['secret']);
        self::assertEquals($device->getGroupNameObject()->getGroupName(), $payload['groupName']['groupName']);
        self::assertEquals($device->getGroupNameObject()->getGroupNameID(), $payload['groupName']['groupNameID']);
        self::assertEquals($device->getRoomObject()->getRoomID(), $payload['room']['roomID']);
        self::assertEquals($device->getRoomObject()->getRoom(), $payload['room']['roomName']);
        self::assertArrayHasKey('ipAddress', $payload);
        self::assertArrayHasKey('externalIpAddress', $payload);
        self::assertEquals($device->getRoles(), $payload['roles']);
    }


    public function test_get_all_devices_doesnt_return_devices_of_group_user_is_not_assigned_to_regular_user(): void
    {
        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($user);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupNameID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_DEVICES_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],

        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);

        $allDevices = $this->deviceRepository->findAll();

        foreach ($allDevices as $device) {
            if ($device->getGroupNameObject()->getGroupNameID() === $groupsUserIsAssignedTo) {
                self::assertContains($device->getDeviceID(), $payload);
            }
            if ($device->getGroupNameObject()->getGroupNameID() !== $groupsUserIsAssignedTo) {
                self::assertNotContains($device->getDeviceID(), $payload);
            }
        }
        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_get_all_devices_returns_all_devices_admin(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_DEVICES_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],

        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);

        $allDevices = $this->deviceRepository->findAll();

        $deviceIDs = array_column($payload, 'deviceNameID');
        foreach ($allDevices as $device) {
            self::assertContains($device->getDeviceID(), $deviceIDs, $device->getDeviceName());
        }
    }

    /**
     * @dataProvider limitAndOffsetDataProvider
     */
    public function test_limit_and_offset_works_admin_user(int $limit, int $offset): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_DEVICES_URL,
            ['limit' => $limit, 'offset' => $offset],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],

        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
        self::assertCount($limit, $payload);

        $devices = $this->deviceRepository->findBy(
            [],
            ['deviceName' => 'ASC'],
        );

        $devicesThatShouldBeReturned = array_slice($devices, $offset, $limit);
        $devicesThatShouldNotBeReturned = array_slice($devices, 0, $limit - 1);
        $deviceIds = array_column($payload, 'deviceNameID');

        foreach ($devicesThatShouldBeReturned as $device) {
            self::assertContains($device->getDeviceID(), $deviceIds);
        }

        foreach ($devicesThatShouldNotBeReturned as $device) {
            self::assertNotContains($device->getDeviceID(), $deviceIds);
        }
    }

    public function limitAndOffsetDataProvider(): Generator
    {
        yield [
            'limit' => 1,
            'offset' => 1,
        ];

        yield [
            'limit' => 2,
            'offset' => 1,
        ];

        yield [
            'limit' => 2,
            'offset' => 1,
        ];

        yield [
            'limit' => 2,
            'offset' => 2,
        ];

        yield [
            'limit' => 3,
            'offset' => 4,
        ];

        yield [
            'limit' => 4,
            'offset' => 3,
        ];
    }

    /**
     * @dataProvider limitAndOffsetWrongDataProvider
     */
    public function test_limit_and_offset_incorrect_data_types_admin_user(mixed $limit, mixed $offset, array $message = []): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_DEVICES_URL,
            ['limit' => $limit, 'offset' => $offset],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],

        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $title = $responseData['title'];
        if (empty($responseData['errors'])) {
            self::fail('No errors or payload returned');
        }
        $errors = $responseData['errors'];

        self::assertEquals($message, $errors);
        self::assertEquals(GetDeviceController::BAD_REQUEST_NO_DATA_RETURNED, $title);
    }

    public function limitAndOffsetWrongDataProvider(): Generator
    {
        yield [
            'limit' => [],
            'offset' => 1,
            'messages' => [
                "limit must be an int|null you have provided array",
            ],
        ];

        yield [
            'limit' => 2,
            'offset' => [],
            'messages' => [
                'offset must be an int|null you have provided array',
            ],
        ];

        yield [
            'limit' => 'string',
            'offset' => 1,
            'messages' => [
                'limit must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'limit' => 2,
            'offset' => 'string',
            'messages' => [
                'offset must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'limit' => false,
            'offset' => 4,
            'messages' => [
                'limit must be an int|null you have provided ""',
            ],
        ];

        // true counts as 1 which is valid
//        yield [
//            'limit' => true,
//            'offset' => 3,
//            'messages' => [
//                'limit must be an int|null you have provided "1"',
//            ],
//        ];

        yield [
            'limit' => [],
            'offset' => [],
            'messages' => [
                'limit must be an int|null you have provided array',
                'offset must be an int|null you have provided array',
            ],
        ];
    }




    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_deleting_device_wrong_http_method_single(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::GET_SINGLE_DEVICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_deleting_device_wrong_http_method_all(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::GET_SINGLE_DEVICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_DELETE],
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
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
