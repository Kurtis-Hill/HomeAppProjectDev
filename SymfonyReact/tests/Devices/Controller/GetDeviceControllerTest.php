<?php

namespace App\Tests\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\Services\PaginationCalculator;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Devices\Controller\GetDeviceController;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
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

    private const GET_SINGLE_DEVICE_URL = CommonURL::USER_HOMEAPP_API_URL . 'user-device/%d/get';

    private const GET_ALL_DEVICES_URL = CommonURL::USER_HOMEAPP_API_URL . 'user-device/all';

    private ?string $adminToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private DeviceRepository $deviceRepository;

    private UserRepository $userRepository;

    private GroupRepository $groupNameRepository;

    private SensorRepository $sensorRepository;

    private User $regularUserOne;

    private User $adminUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->adminToken = $this->setUserToken($this->client);


        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupNameRepository = $this->entityManager->getRepository(Group::class);
        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->regularUserOne = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $this->adminUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }

    public function test_get_device_admin_only_response(): void
    {
        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findAll();
        $device = $devices[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals($device->getDeviceID(), $payload['deviceID']);
        self::assertEquals($device->getDeviceName(), $payload['deviceName']);
        self::assertEquals($device->getIpAddress(), $payload['ipAddress']);
        self::assertEquals($device->getExternalIpAddress(), $payload['externalIpAddress']);
        self::assertTrue($payload['canEdit']);
        self::assertTrue($payload['canDelete']);
        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_get_device_admin_full_response(): void
    {
        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findAll();

        foreach ($devices as $deviceToTest) {
            $sensors = $this->sensorRepository->findBy(['deviceID' => $deviceToTest->getDeviceID()]);
            if (!empty($sensors)) {
                $device = $deviceToTest;
            }
        }
        if (!isset($device)) {
            self::fail('No device with sensors found');
        }


        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
        self::assertEquals($device->getDeviceID(), $payload['deviceID']);
        self::assertEquals($device->getDeviceName(), $payload['deviceName']);
        self::assertEquals($device->getGroupObject()->getGroupName(), $payload['group']['groupName']);
        self::assertEquals($device->getGroupObject()->getGroupID(), $payload['group']['groupID']);
        self::assertEquals($device->getRoomObject()->getRoomID(), $payload['room']['roomID']);
        self::assertEquals($device->getRoomObject()->getRoom(), $payload['room']['roomName']);
        self::assertEquals($device->getIpAddress(), $payload['ipAddress']);
        self::assertEquals($device->getExternalIpAddress(), $payload['externalIpAddress']);
        self::assertTrue($payload['canEdit']);
        self::assertTrue($payload['canDelete']);

        $createdByResponse = $payload['createdBy'];
        $createdByUser = $device->getCreatedBy();

        self::assertEquals($createdByUser->getUserID(), $createdByResponse['userID']);
        self::assertEquals($createdByUser->getFirstName(), $createdByResponse['firstName']);
        self::assertEquals($createdByUser->getLastName(), $createdByResponse['lastName']);
        self::assertEquals($createdByUser->getEmail(), $createdByResponse['email']);

        self::assertEquals($createdByUser->getGroup()->getGroupID(), $createdByResponse['group']['groupID']);
        self::assertEquals($createdByUser->getGroup()->getGroupName(), $createdByResponse['group']['groupName']);
        self::assertEquals($createdByUser->getProfilePic(), $createdByResponse['profilePicture']);

        $deviceSensors = $this->sensorRepository->findBy(['deviceID' => $device->getDeviceID()]);

        self::assertArrayHasKey('sensorData', $payload);
        $sensorData = $payload['sensorData'];

        $sensorIDs = array_column($sensorData, 'sensorID');

        foreach ($deviceSensors as $sensor) {
            if (!in_array($sensor->getSensorID(), $sensorIDs, true)) {
                self::fail('Sensor not found in response');
            }
        }

        foreach ($sensorData as $data) {
            self::assertArrayHasKey('sensorID', $data);
            self::assertArrayHasKey('sensorName', $data);
            self::assertArrayHasKey('createdBy', $data);
            self::assertArrayHasKey('device', $data);
            self::assertArrayHasKey('sensorType', $data);
            self::assertArrayHasKey('sensorReadingTypes', $data);
        }
    }

    public function test_get_device_of_group_user_is_not_assigned_to_regular_user(): void
    {
        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $groupsUserIsNotAssignedTo = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->regularUserOne);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotAssignedTo]);
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
        $groupsUserIsNotAssignedTo = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->adminUser);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];

        self::assertNotEmpty($payload);
    }

    public function test_get_device_of_group_user_is_assigned_to_regular_user_full_response(): void
    {
        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserOne);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => 'full'],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
        self::assertEquals($device->getDeviceID(), $payload['deviceID']);
        self::assertEquals($device->getDeviceName(), $payload['deviceName']);
        self::assertEquals($device->getGroupObject()->getGroupName(), $payload['group']['groupName']);
        self::assertEquals($device->getGroupObject()->getGroupID(), $payload['group']['groupID']);
        self::assertEquals($device->getRoomObject()->getRoomID(), $payload['room']['roomID']);
        self::assertEquals($device->getRoomObject()->getRoom(), $payload['room']['roomName']);
        self::assertEquals($device->getIpAddress(), $payload['ipAddress']);
        self::assertEquals($device->getExternalIpAddress(), $payload['externalIpAddress']);
        self::assertTrue($payload['canEdit']);
        self::assertTrue($payload['canDelete']);

        $createdByResponse = $payload['createdBy'];
        $createdByUser = $device->getCreatedBy();

        self::assertEquals($createdByUser->getUserID(), $createdByResponse['userID']);
        self::assertEquals($createdByUser->getFirstName(), $createdByResponse['firstName']);
        self::assertEquals($createdByUser->getLastName(), $createdByResponse['lastName']);
        self::assertEquals($createdByUser->getEmail(), $createdByResponse['email']);
        self::assertEquals($createdByUser->getGroup()->getGroupID(), $createdByResponse['group']['groupID']);
        self::assertEquals($createdByUser->getGroup()->getGroupName(), $createdByResponse['group']['groupName']);
        self::assertEquals($createdByUser->getProfilePic(), $createdByResponse['profilePicture']);

        $deviceSensors = $this->sensorRepository->findBy(['deviceID' => $device->getDeviceID()]);

        self::assertArrayHasKey('sensorData', $payload);
        $sensorData = $payload['sensorData'];

        $sensorIDs = array_column($sensorData, 'sensorID');

        foreach ($deviceSensors as $sensor) {
            if (!in_array($sensor->getSensorID(), $sensorIDs, true)) {
                self::fail('Sensor not found in response');
            }
        }

        foreach ($sensorData as $data) {
            self::assertArrayHasKey('sensorID', $data);
            self::assertArrayHasKey('sensorName', $data);
            self::assertArrayHasKey('createdBy', $data);
            self::assertArrayHasKey('device', $data);
            self::assertArrayHasKey('sensorType', $data);
            self::assertArrayHasKey('sensorReadingTypes', $data);
        }
    }

    public function test_get_device_of_group_user_is_assigned_to_regular_user_response_type_only(): void
    {
        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserOne);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );
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
        self::assertEquals($device->getDeviceID(), $payload['deviceID']);
        self::assertEquals($device->getDeviceName(), $payload['deviceName']);
        self::assertEquals($device->getIpAddress(), $payload['ipAddress']);
        self::assertEquals($device->getExternalIpAddress(), $payload['externalIpAddress']);
    }

    public function test_get_device_of_group_user_is_assigned_to_admin(): void
    {
        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($this->adminUser);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }
        $device = $devices[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_DEVICE_URL, $device->getDeviceID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => 'full'],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);
        $title = $responseData['title'];
        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_get_all_devices_doesnt_return_devices_of_group_user_is_not_assigned_to_regular_user(): void
    {
        $groupsUserIsAssignedTo = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserOne);

        /** @var Devices[] $devices */
        $devices = $this->deviceRepository->findBy(['groupID' => $groupsUserIsAssignedTo]);
        if (empty($devices)) {
            self::fail('No devices found for this user');
        }

        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );
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
            if ($device->getGroupObject()->getGroupID() === $groupsUserIsAssignedTo) {
                self::assertContains($device->getDeviceID(), $payload);
            }
            if ($device->getGroupObject()->getGroupID() !== $groupsUserIsAssignedTo) {
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
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];

        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);

        $allDevices = $this->deviceRepository->findAll();

        $deviceIDs = array_column($payload, 'deviceID');
        foreach ($allDevices as $device) {
            self::assertContains($device->getDeviceID(), $deviceIDs, $device->getDeviceName());
        }
    }

    /**
     * @dataProvider limitAndPageDataProvider
     */
    public function test_limit_and_page_works_admin_user(int $limit, int $page): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_DEVICES_URL,
            ['limit' => $limit, 'page' => $page],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];
        $title = $responseData['title'];
        self::assertEquals(GetDeviceController::REQUEST_SUCCESSFUL, $title);
        if (is_array($payload)) {
            self::assertCount($limit, $payload);
            $deviceIds = array_column($payload, 'deviceID');

            $devices = $this->deviceRepository->findBy(
                [],
                ['deviceName' => 'ASC'],
            );
            $devicesThatShouldBeReturned = array_slice($devices, PaginationCalculator::calculateOffset($limit, $page), $limit);
            $devicesThatShouldNotBeReturned = array_slice($devices, $page * 2);
            foreach ($devicesThatShouldBeReturned as $device) {
                self::assertContains($device->getDeviceID(), $deviceIds);
            }
            foreach ($devicesThatShouldNotBeReturned as $device) {
                self::assertNotContains($device->getDeviceID(), $deviceIds);
            }
        } else {
            self::assertEquals(GetDeviceController::NO_RESPONSE_MESSAGE, $payload);
        }
    }

    public function limitAndPageDataProvider(): Generator
    {
        yield [
            'limit' => 1,
            'page' => 1,
        ];

        yield [
            'limit' => 2,
            'page' => 1,
        ];

        yield [
            'limit' => 2,
            'page' => 1,
        ];

        yield [
            'limit' => 2,
            'page' => 2,
        ];

        yield [
            'limit' => 3,
            'page' => 4,
        ];

        //@TODO fix this test needs to be smarter and calculate expected size
//        yield [
//            'limit' => 4,
//            'page' => 3,
//        ];
    }

    /**
     * @dataProvider limitAndPageWrongDataProvider
     */
    public function test_limit_and_page_incorrect_data_types_admin_user(mixed $limit, mixed $page, array $message = []): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_DEVICES_URL,
            ['limit' => $limit, 'page' => $page],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken],
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

    public function limitAndPageWrongDataProvider(): Generator
    {
        yield [
            'limit' => [],
            'page' => 1,
            'messages' => [
                "limit must be an int|null you have provided array",
            ],
        ];

        yield [
            'limit' => 2,
            'page' => [],
            'messages' => [
                'page must be an int|null you have provided array',
            ],
        ];

        yield [
            'limit' => 'string',
            'page' => 1,
            'messages' => [
                'limit must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'limit' => 2,
            'page' => 'string',
            'messages' => [
                'page must be an int|null you have provided "string"',
            ],
        ];

        yield [
            'limit' => false,
            'page' => 4,
            'messages' => [
                'limit must be an int|null you have provided ""',
            ],
        ];

        // true counts as 1 which is valid
//        yield [
//            'limit' => true,
//            'page' => 3,
//            'messages' => [
//                'limit must be an int|null you have provided "1"',
//            ],
//        ];

        yield [
            'limit' => [],
            'page' => [],
            'messages' => [
                'page must be an int|null you have provided array',
                'limit must be an int|null you have provided array',
            ],
        ];
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_getting_device_wrong_http_method_single(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::GET_SINGLE_DEVICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_getting_device_wrong_http_method_all(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::GET_SINGLE_DEVICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken],
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
}
