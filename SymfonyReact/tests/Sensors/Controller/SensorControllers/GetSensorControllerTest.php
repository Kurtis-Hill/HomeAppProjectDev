<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\Builders\Request\RequestDTOBuilder;
use App\Common\DTO\Request\RequestDTO;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Sensors\Controller\SensorControllers\GetSensorController;
use App\Sensors\Controller\SensorControllers\GetSingleSensorsController;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Repository\Sensors\ORM\SensorTypeRepository;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
use App\User\Repository\ORM\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetSensorControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_SENSOR_URL = '/HomeApp/api/user/sensors/all';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?Devices $device;

    private SensorRepositoryInterface $sensorRepository;

    private UserRepositoryInterface $userRepository;

    private GroupRepository $groupNameRepository;

    private DeviceRepositoryInterface $deviceRepository;

    private SensorTypeRepository $sensorTypeRepository;

    private User $adminUser;

    private User $regularUserTwo;

    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->adminUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);

        $this->device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name']]);
        $this->userToken = $this->setUserToken($this->client);
        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupNameRepository = $this->entityManager->getRepository(Group::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->sensorTypeRepository = $this->entityManager->getRepository(SensorType::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider sendingIncorrectDataTypesAndChoicesDataProvider
     */
    public function test_sending_incorrect_data_types_and_choices(array $dataToSend, array $errorsMessages): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals($errorsMessages, $responseData['errors']);
        self::assertEquals(GetSensorController::BAD_REQUEST_NO_DATA_RETURNED, $responseData['title']);
    }

    public function sendingIncorrectDataTypesAndChoicesDataProvider(): Generator
    {
        yield [
            'dataToSend' => [
                'limit' => 101
            ],
            'errorMessages' => [
                'limit must be greater than 1 but less than 100'
            ],
        ];

        yield [
            'dataToSend' => [
                'limit' => 0
            ],
            'errorMessages' => [
                'limit must be greater than 1 but less than 100'
            ],
        ];

        yield [
            'dataToSend' => [
                'limit' => -1
            ],
            'errorMessages' => [
                'limit must be greater than 1 but less than 100'
            ],
        ];

        yield [
            'dataToSend' => [
                'limit' => 'string'
            ],
            'errorMessages' => [
                'limit must be an int|null you have provided "string"'
            ],
        ];

        yield [
            'dataToSend' => [
                'page' => 'string'
            ],
            'errorMessages' => [
                'page must be an int|null you have provided "string"'
            ],
        ];

        yield [
            'dataToSend' => [
                'page' => -1
            ],
            'errorMessages' => [
                'page must be greater than "-1"'
            ],
        ];

        yield [
            'dataToSend' => [
                'deviceIDs' => 'string'
            ],
            'errorMessages' => [
                'deviceIDs must be a array|null you have provided "string"'
            ],
        ];

        yield [
            'dataToSend' => [
                'deviceIDs' => 1
            ],
            'errorMessages' => [
                'deviceIDs must be a array|null you have provided "1"'
            ],
        ];

        yield [
            'dataToSend' => [
                'deviceNames' => 'string'
            ],
            'errorMessages' => [
                'deviceNames must be a array|null you have provided "string"'
            ],
        ];

        yield [
            'dataToSend' => [
                'deviceNames' => 1
            ],
            'errorMessages' => [
                'deviceNames must be a array|null you have provided "1"'
            ],
        ];

        yield [
            'dataToSend' => [
                'groupIDs' => 'string'
            ],
            'errorMessages' => [
                'groupIDs must be a array|null you have provided "string"'
            ],
        ];

        yield [
            'dataToSend' => [
                'groupIDs' => 1
            ],
            'errorMessages' => [
                'groupIDs must be a array|null you have provided "1"'
            ],
        ];
    }

    public function test_getting_devicesIDs_not_assinged_to(): void
    {
        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->regularUserTwo);

        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsNotApartOf]);

        $deviceIDs = array_map(function (Devices $device) {
            return $device->getDeviceID();
        }, $devicesUserIsNotApartOf);

        $dataToSend = [
            'deviceIDs' => $deviceIDs
        ];

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_MULTI_STATUS);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertEmpty($payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::SOME_ISSUES_WITH_REQUEST, $title);

        $errors = $responseData['errors'];
        self::assertCount(count($deviceIDs), $errors);
    }

    public function test_getting_device_names_not_assigned_to(): void
    {
        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->regularUserTwo);

        /** @var Devices[] $devicesUserIsNotApartOf */
        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsNotApartOf]);

        $deviceNames = array_map(function (Devices $device) {
            return $device->getDeviceName();
        }, $devicesUserIsNotApartOf);

        $dataToSend = [
            'deviceNames' => $deviceNames
        ];

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_MULTI_STATUS);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertEmpty($payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::SOME_ISSUES_WITH_REQUEST, $title);

        $errors = $responseData['errors'];
        self::assertCount(count($deviceNames), $errors);
    }

    public function test_getting_groupIDs_not_assigned_to(): void
    {
        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->regularUserTwo);


        $groupIDs = array_map(static function (Group $group) {
            return $group->getGroupID();
        }, $groupsNotApartOf);

        $dataToSend = [
            'groupIDs' => $groupIDs
        ];

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_MULTI_STATUS);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertEmpty($payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::SOME_ISSUES_WITH_REQUEST, $title);

        $errors = $responseData['errors'];

        self::assertCount(count($groupIDs), $errors);
    }

    public function test_regular_user_can_get_devices_ids_is_assigned_to(): void
    {
        /** @var Group[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserTwo);

        /** @var Devices[] $devicesUserIsApartOf */
        $devicesUserIsApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);

        $deviceIDs = array_map(function (Devices $device) {
            return $device->getDeviceID();
        }, $devicesUserIsApartOf);

        $dataToSend = [
            'deviceIDs' => $deviceIDs
        ];
        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $sensors = $this->sensorRepository->findBy(['deviceID' => $deviceIDs]);
        self::assertCount(count($sensors), $payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_regular_user_can_get_device_names_is_assigned_to(): void
    {
        /** @var Group[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserTwo);

        /** @var Devices[] $devicesUserIsApartOf */
        $devicesUserIsApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);

        $deviceNames = array_map(function (Devices $device) {
            return $device->getDeviceName();
        }, $devicesUserIsApartOf);

        $dataToSend = [
            'deviceNames' => $deviceNames
        ];

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $sensors = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsApartOf]);
        self::assertCount(count($sensors), $payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_regular_user_can_get_groupIDs_is_assigned_to(): void
    {
        /** @var Group[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserTwo);

        /** @var Devices[] $devicesUserIsApartOf */
        $devicesUserIsApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);

        $groupIDs = array_map(function (Group $group) {
            return $group->getGroupID();
        }, $groupsUserIsApartOf);

        $dataToSend = [
            'groupIDs' => $groupIDs
        ];

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $sensors = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsApartOf]);
        self::assertCount(count($sensors), $payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_admins_can_get_devices_not_assigned_to(): void
    {
        /** @var Group[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->adminUser);

        /** @var Devices[] $devicesUserIsNotApartOf */
        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);

        $deviceIDs = array_map(function (Devices $device) {
            return $device->getDeviceID();
        }, $devicesUserIsNotApartOf);

        $dataToSend = [
            'deviceIDs' => $deviceIDs
        ];

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $sensors = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsNotApartOf]);
        self::assertCount(count($sensors), $payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_admins_can_get_device_names_not_assigned_to(): void
    {
        /** @var Group[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->adminUser);

        /** @var Devices[] $devicesUserIsNotApartOf */
        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);

        $deviceNames = array_map(function (Devices $device) {
            return $device->getDeviceName();
        }, $devicesUserIsNotApartOf);

        $dataToSend = [
            'deviceNames' => $deviceNames
        ];

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $sensors = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsNotApartOf]);
        self::assertCount(count($sensors), $payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_admins_can_get_groupIDs_not_assigned_to(): void
    {
        /** @var Group[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->adminUser);

        /** @var Devices[] $devicesUserIsNotApartOf */
        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);

        $groupIDs = array_map(function (Group $group) {
            return $group->getGroupID();
        }, $groupsUserIsNotApartOf);

        $dataToSend = [
            'groupIDs' => $groupIDs
        ];

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $sensors = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsNotApartOf]);
        self::assertCount(count($sensors), $payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::REQUEST_SUCCESSFUL, $title);
    }

    public function test_part_response_data(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            ['responseType' => RequestDTOBuilder::REQUEST_TYPE_ONLY],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $allSensors = $this->sensorRepository->findAll();
        self::assertCount(count($allSensors), $payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::REQUEST_SUCCESSFUL, $title);

        foreach ($payload as $sensorData) {
            $sensor = $this->sensorRepository->find($sensorData['sensorNameID']);

            self::assertEquals($sensor->getSensorID(), $sensorData['sensorNameID']);
            self::assertEquals($sensor->getSensorName(), $sensorData['sensorName']);
            self::assertEquals($sensor->getSensorTypeObject()->getSensorType(), $sensorData['sensorType']);
            self::assertEquals($sensor->getDevice()->getDeviceName(), $sensorData['deviceName']);
            self::assertEquals($sensor->getCreatedBy()->getEmail(), $sensorData['createdBy']);
        }
    }

    public function test_full_response_data(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_URL,
            ['responseType' => RequestDTOBuilder::REQUEST_TYPE_FULL],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $allSensors = $this->sensorRepository->findAll();
        self::assertCount(count($allSensors), $payload);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::REQUEST_SUCCESSFUL, $title);

        foreach ($payload as $sensorData) {
            $sensorObject = $this->sensorRepository->find($sensorData['sensor']['sensorNameID']);

            if (
                $sensorObject->getSensorTypeObject()->getSensorType() === Dht::NAME
                || $sensorObject->getSensorTypeObject()->getSensorType() === Dallas::NAME
                || $sensorObject->getSensorTypeObject()->getSensorType() === Bmp::NAME
            ) {
                $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
                /** @var Temperature $temperature */
                $temperature = $temperatureRepository->find($sensorData['sensorReadingTypes'][Temperature::READING_TYPE]['temperatureID']);
                self::assertEquals($temperature->getSensorID(), $sensorData['sensorReadingTypes'][Temperature::READING_TYPE]['temperatureID']);
                self::assertEquals($temperature->getCurrentReading(), $sensorData['sensorReadingTypes'][Temperature::READING_TYPE]['currentReading']);
                self::assertEquals($temperature->getHighReading(), $sensorData['sensorReadingTypes'][Temperature::READING_TYPE]['highReading']);
                self::assertEquals($temperature->getLowReading(), $sensorData['sensorReadingTypes'][Temperature::READING_TYPE]['lowReading']);
                self::assertEquals($temperature->getConstRecord(), $sensorData['sensorReadingTypes'][Temperature::READING_TYPE]['constRecorded']);
            }
            if (
                $sensorObject->getSensorTypeObject()->getSensorType() === Dht::NAME
                || $sensorObject->getSensorTypeObject()->getSensorType() === Bmp::NAME
            ) {
                $humidityRepository = $this->entityManager->getRepository(Humidity::class);
                /** @var Humidity $humidity */
                $humidity = $humidityRepository->find($sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['humidityID']);
                self::assertEquals($humidity->getSensorID(), $sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['humidityID']);
                self::assertEquals($humidity->getCurrentReading(), $sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['currentReading']);
                self::assertEquals($humidity->getHighReading(), $sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['highReading']);
                self::assertEquals($humidity->getLowReading(), $sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['lowReading']);
                self::assertEquals($humidity->getConstRecord(), $sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['constRecorded']);
            }

            if ($sensorObject->getSensorTypeObject()->getSensorType() === Bmp::NAME) {
                $latitudeRepository = $this->entityManager->getRepository(Latitude::class);
                /** @var Latitude $latitude */
                $latitude = $latitudeRepository->find($sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['latitudeID']);
                self::assertEquals($latitude->getSensorID(), $sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['latitudeID']);
                self::assertEquals($latitude->getCurrentReading(), $sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['currentReading']);
                self::assertEquals($latitude->getHighReading(), $sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['highReading']);
                self::assertEquals($latitude->getLowReading(), $sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['lowReading']);
                self::assertEquals($latitude->getConstRecord(), $sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['constRecorded']);
            }

            if ($sensorObject->getSensorTypeObject()->getSensorType() === Soil::NAME) {
                $analogRepository = $this->entityManager->getRepository(Analog::class);
                /** @var Analog $analog */
                $analog = $analogRepository->find($sensorData['sensorReadingTypes'][Analog::READING_TYPE]['analogID']);
                self::assertEquals($analog->getSensorID(), $sensorData['sensorReadingTypes'][Analog::READING_TYPE]['analogID']);
                self::assertEquals($analog->getCurrentReading(), $sensorData['sensorReadingTypes'][Analog::READING_TYPE]['currentReading']);
                self::assertEquals($analog->getHighReading(), $sensorData['sensorReadingTypes'][Analog::READING_TYPE]['highReading']);
                self::assertEquals($analog->getLowReading(), $sensorData['sensorReadingTypes'][Analog::READING_TYPE]['lowReading']);
                self::assertEquals($analog->getConstRecord(), $sensorData['sensorReadingTypes'][Analog::READING_TYPE]['constRecorded']);
            }

            self::assertEquals($sensorObject->getSensorID(), $sensorData['sensor']['sensorNameID']);
            self::assertEquals($sensorObject->getSensorName(), $sensorData['sensor']['sensorName']);
            self::assertEquals($sensorObject->getSensorTypeObject()->getSensorType(), $sensorData['sensor']['sensorType']);
            self::assertEquals($sensorObject->getDevice()->getDeviceName(), $sensorData['sensor']['deviceName']);
            self::assertEquals($sensorObject->getCreatedBy()->getEmail(), $sensorData['sensor']['createdBy']);
        }
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::GET_SENSOR_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): Generator
    {
        yield [Request::METHOD_POST];
        yield [Request::METHOD_PUT];
        yield [Request::METHOD_PATCH];
        yield [Request::METHOD_DELETE];
    }
}
