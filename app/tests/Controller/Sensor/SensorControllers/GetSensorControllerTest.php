<?php

namespace App\Tests\Controller\Sensor\SensorControllers;

use App\Controller\Sensor\SensorControllers\GetSensorController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Device\Devices;
use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardView;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Repository\Sensor\Sensors\ORM\SensorTypeRepository;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Repository\User\ORM\GroupRepository;
use App\Repository\User\ORM\UserRepositoryInterface;
use App\Repository\UserInterface\ORM\CardRepositories\CardViewRepository;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetSensorControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_ALL_SENSORS_URL = '/HomeApp/api/user/sensors';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?Devices $device;

    private SensorRepositoryInterface $sensorRepository;

    private UserRepositoryInterface $userRepository;

    private GroupRepository $groupNameRepository;

    private DeviceRepositoryInterface $deviceRepository;

    private SensorTypeRepository $sensorTypeRepository;

    private CardViewRepository $cardViewRepository;

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
        $this->sensorTypeRepository = $this->entityManager->getRepository(AbstractSensorType::class);
        $this->cardViewRepository = $this->entityManager->getRepository(CardView::class);
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
            self::GET_ALL_SENSORS_URL,
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
                'page must be greater than 0'
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
        /** @var \App\Entity\User\Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->regularUserTwo);

        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsNotApartOf]);

        $deviceIDs = array_map(static function (Devices $device) {
            return $device->getDeviceID();
        }, $devicesUserIsNotApartOf);

        $dataToSend = [
            'deviceIDs' => $deviceIDs
        ];

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_SENSORS_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $title = $responseData['title'];
        self::assertEquals(\App\Controller\Sensor\SensorControllers\GetSensorController::BAD_REQUEST_NO_DATA_RETURNED, $title);

        $errors = $responseData['errors'];
        self::assertCount(count($deviceIDs), $errors);
    }

    public function test_getting_device_names_not_assigned_to(): void
    {
        /** @var \App\Entity\User\Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->regularUserTwo);

        /** @var \App\Entity\Device\Devices[] $devicesUserIsNotApartOf */
        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsNotApartOf]);

        $deviceNames = array_map(static function (Devices $device) {
            return $device->getDeviceName();
        }, $devicesUserIsNotApartOf);

        $dataToSend = [
            'deviceNames' => $deviceNames
        ];

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_SENSORS_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::BAD_REQUEST_NO_DATA_RETURNED, $title);

        $errors = $responseData['errors'];
        self::assertCount(count($deviceNames), $errors);
    }

    public function test_getting_groupIDs_not_assigned_to(): void
    {
        /** @var \App\Entity\User\Group[] $groupsUserNotApartOf */
        $groupsUserNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->regularUserTwo);

        $groupIDs  = array_map(static fn (Group $group) => $group->getGroupID(), $groupsUserNotApartOf);
        $dataToSend = [
            'groupIDs' => $groupIDs
        ];

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_SENSORS_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $title = $responseData['title'];
        self::assertEquals(GetSensorController::BAD_REQUEST_NO_DATA_RETURNED, $title);

        $errors = $responseData['errors'];
        // -1 for the group that had no sensors
        self::assertCount(count($groupIDs) - 1, $errors);
    }

    public function test_regular_user_can_get_devices_ids_is_assigned_to(): void
    {
        /** @var \App\Entity\User\Group[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserTwo);

        /** @var \App\Entity\Device\Devices[] $devicesUserIsApartOf */
        $devicesUserIsApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);

        $deviceIDs = array_map(static function (Devices $device) {
            return $device->getDeviceID();
        }, $devicesUserIsApartOf);

        $dataToSend = [
            'deviceIDs' => $deviceIDs
        ];
        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_SENSORS_URL,
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
        /** @var \App\Entity\User\Group[] $groupsUserIsApartOf */
        $groupsUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserTwo);

        /** @var \App\Entity\Device\Devices[] $devicesUserIsApartOf */
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
            self::GET_ALL_SENSORS_URL,
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
        self::assertEquals(\App\Controller\Sensor\SensorControllers\GetSensorController::REQUEST_SUCCESSFUL, $title);
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
            self::GET_ALL_SENSORS_URL,
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
        /** @var \App\Entity\User\Group[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->adminUser);

        /** @var \App\Entity\Device\Devices[] $devicesUserIsNotApartOf */
        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);

        $deviceIDs = array_map(function (Devices $device) {
            return $device->getDeviceID();
        }, $devicesUserIsNotApartOf);

        $dataToSend = [
            'deviceIDs' => $deviceIDs
        ];

        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_SENSORS_URL,
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

        /** @var \App\Entity\Device\Devices[] $devicesUserIsNotApartOf */
        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);

        $deviceNames = array_map(function (Devices $device) {
            return $device->getDeviceName();
        }, $devicesUserIsNotApartOf);

        $dataToSend = [
            'deviceNames' => $deviceNames
        ];

        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_SENSORS_URL,
            $dataToSend,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        self::assertArrayNotHasKey('errors', $responseData);

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
            self::GET_ALL_SENSORS_URL,
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

    public function test_part_response_data_admin(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_SENSORS_URL,
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::ONLY->value],
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
        self::assertEquals(\App\Controller\Sensor\SensorControllers\GetSensorController::REQUEST_SUCCESSFUL, $title);

        foreach ($payload as $sensorData) {
            $sensor = $this->sensorRepository->find($sensorData['sensorID']);

            self::assertEquals($sensor->getSensorID(), $sensorData['sensorID']);
            self::assertEquals($sensor->getSensorName(), $sensorData['sensorName']);
            self::assertTrue($sensorData['canEdit']);
            self::assertTrue($sensorData['canDelete']);
        }
    }

    public function test_full_response_data_admin(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_SENSORS_URL,
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payloads = $responseData['payload'];
        self::assertNotEmpty($payloads);

        $allSensors = $this->sensorRepository->findAll();
        self::assertCount(count($allSensors), $payloads);

        $title = $responseData['title'];
        self::assertEquals(\App\Controller\Sensor\SensorControllers\GetSensorController::REQUEST_SUCCESSFUL, $title);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(\App\Controller\Sensor\SensorControllers\GetSingleSensorsController::REQUEST_SUCCESSFUL, $responseData['title']);

        self::assertNotEmpty($responseData['payload']);
        $sensorData = $responseData['payload'];

        self::assertNotNull($sensorData);

        $sensorReadingTypePass = 0;
        foreach ($sensorData as $singleSensorData) {
            if (empty($singleSensorData['sensorReadingTypes'])) {
                continue;
            }
            ++$sensorReadingTypePass;
            $sensorObject = $this->sensorRepository->find($singleSensorData['sensorID']);

            $sensorReadingTypes = $singleSensorData['sensorReadingTypes'];
            if (
                $sensorObject->getSensorTypeObject()::getReadingTypeName() === Dht::NAME
                || $sensorObject->getSensorTypeObject()::getReadingTypeName() === Dallas::NAME
                || $sensorObject->getSensorTypeObject()::getReadingTypeName() === Bmp::NAME
                || $sensorObject->getSensorTypeObject()::getReadingTypeName() === Sht::NAME
            ) {
                $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
                /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature $temperature */
                $temperature = $temperatureRepository->find($sensorReadingTypes[Temperature::READING_TYPE]['temperatureID']);
                self::assertEquals($temperature->getSensorID(), $sensorReadingTypes[Temperature::READING_TYPE]['temperatureID']);
                self::assertEquals($temperature->getCurrentReading(), $sensorReadingTypes[Temperature::READING_TYPE]['currentReading']);
                self::assertEquals($temperature->getHighReading(), $sensorReadingTypes[Temperature::READING_TYPE]['highReading']);
                self::assertEquals($temperature->getLowReading(), $sensorReadingTypes[Temperature::READING_TYPE]['lowReading']);
                self::assertEquals($temperature->getConstRecord(), $sensorReadingTypes[Temperature::READING_TYPE]['constRecord']);
                self::assertEquals($temperature->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Temperature::READING_TYPE]['baseReadingTypeID']);
                self::assertEquals(AbstractSensorType::STANDARD_READING_SENSOR_TYPE, $sensorReadingTypes[Temperature::READING_TYPE]['sensorType']);
                self::assertEquals(Temperature::READING_TYPE, $sensorReadingTypes[Temperature::READING_TYPE]['readingType']);
            }
            if (
                $sensorObject->getSensorTypeObject()::getReadingTypeName() === Dht::NAME
                || $sensorObject->getSensorTypeObject()::getReadingTypeName() === Bmp::NAME
                || $sensorObject->getSensorTypeObject()::getReadingTypeName() === Sht::NAME
            ) {
                $humidityRepository = $this->entityManager->getRepository(Humidity::class);
                /** @var Humidity $humidity */
                $humidity = $humidityRepository->find($singleSensorData['sensorReadingTypes'][Humidity::READING_TYPE]['humidityID']);
                self::assertEquals($humidity->getSensorID(), $singleSensorData['sensorReadingTypes'][Humidity::READING_TYPE]['humidityID']);
                self::assertEquals($humidity->getCurrentReading(), $singleSensorData['sensorReadingTypes'][Humidity::READING_TYPE]['currentReading']);
                self::assertEquals($humidity->getHighReading(), $singleSensorData['sensorReadingTypes'][Humidity::READING_TYPE]['highReading']);
                self::assertEquals($humidity->getLowReading(), $singleSensorData['sensorReadingTypes'][Humidity::READING_TYPE]['lowReading']);
                self::assertEquals($humidity->getConstRecord(), $singleSensorData['sensorReadingTypes'][Humidity::READING_TYPE]['constRecord']);
                self::assertEquals($humidity->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Humidity::READING_TYPE]['baseReadingTypeID']);
                self::assertEquals(AbstractSensorType::STANDARD_READING_SENSOR_TYPE, $sensorReadingTypes[Humidity::READING_TYPE]['sensorType']);
                self::assertEquals(Humidity::READING_TYPE, $sensorReadingTypes[Humidity::READING_TYPE]['readingType']);
            }

            if ($sensorObject->getSensorTypeObject()::getReadingTypeName() === Bmp::NAME) {
                $latitudeRepository = $this->entityManager->getRepository(Latitude::class);
                /** @var Latitude $latitude */
                $latitude = $latitudeRepository->find($singleSensorData['sensorReadingTypes'][Latitude::READING_TYPE]['latitudeID']);
                self::assertEquals($latitude->getSensorID(), $singleSensorData['sensorReadingTypes'][Latitude::READING_TYPE]['latitudeID']);
                self::assertEquals($latitude->getCurrentReading(), $singleSensorData['sensorReadingTypes'][Latitude::READING_TYPE]['currentReading']);
                self::assertEquals($latitude->getHighReading(), $singleSensorData['sensorReadingTypes'][Latitude::READING_TYPE]['highReading']);
                self::assertEquals($latitude->getLowReading(), $singleSensorData['sensorReadingTypes'][Latitude::READING_TYPE]['lowReading']);
                self::assertEquals($latitude->getConstRecord(), $singleSensorData['sensorReadingTypes'][Latitude::READING_TYPE]['constRecord']);
                self::assertEquals($latitude->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Latitude::READING_TYPE]['baseReadingTypeID']);
                self::assertEquals(AbstractSensorType::STANDARD_READING_SENSOR_TYPE, $sensorReadingTypes[Latitude::READING_TYPE]['sensorType']);
                self::assertEquals(Latitude::READING_TYPE, $sensorReadingTypes[Latitude::READING_TYPE]['readingType']);
            }

            if (
                $sensorObject->getSensorTypeObject()::getReadingTypeName() === Soil::NAME
                || $sensorObject->getSensorTypeObject()::getReadingTypeName() === LDR::NAME
            ) {
                $analogRepository = $this->entityManager->getRepository(Analog::class);
                /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog $analog */
                $analog = $analogRepository->find($singleSensorData['sensorReadingTypes'][Analog::READING_TYPE]['analogID']);
                self::assertEquals($analog->getSensorID(), $singleSensorData['sensorReadingTypes'][Analog::READING_TYPE]['analogID']);
                self::assertEquals($analog->getCurrentReading(), $singleSensorData['sensorReadingTypes'][Analog::READING_TYPE]['currentReading']);
                self::assertEquals($analog->getHighReading(), $singleSensorData['sensorReadingTypes'][Analog::READING_TYPE]['highReading']);
                self::assertEquals($analog->getLowReading(), $singleSensorData['sensorReadingTypes'][Analog::READING_TYPE]['lowReading']);
                self::assertEquals($analog->getConstRecord(), $singleSensorData['sensorReadingTypes'][Analog::READING_TYPE]['constRecord']);
                self::assertEquals($analog->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Analog::READING_TYPE]['baseReadingTypeID']);
                self::assertEquals(AbstractSensorType::STANDARD_READING_SENSOR_TYPE, $sensorReadingTypes[Analog::READING_TYPE]['sensorType']);
                self::assertEquals(Analog::READING_TYPE, $sensorReadingTypes[Analog::READING_TYPE]['readingType']);
            }

            if ($sensorObject->getSensorTypeObject()::getReadingTypeName() === GenericMotion::NAME) {
                $motionRepository = $this->entityManager->getRepository(Motion::class);
                /** @var \App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion $motion */
                $motion = $motionRepository->find($singleSensorData['sensorReadingTypes'][Motion::READING_TYPE]['boolID']);
                self::assertEquals($motion->getBoolID(), $singleSensorData['sensorReadingTypes'][Motion::READING_TYPE]['boolID']);
                self::assertEquals($motion->getCurrentReading(), $singleSensorData['sensorReadingTypes'][Motion::READING_TYPE]['currentReading']);
                self::assertEquals($motion->getExpectedReading(), $singleSensorData['sensorReadingTypes'][Motion::READING_TYPE]['expectedReading']);
                self::assertEquals($motion->getRequestedReading(), $singleSensorData['sensorReadingTypes'][Motion::READING_TYPE]['requestedReading']);
                self::assertEquals($motion->getConstRecord(), $singleSensorData['sensorReadingTypes'][Motion::READING_TYPE]['constRecord']);
                self::assertEquals($motion->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Motion::READING_TYPE]['baseReadingTypeID']);
                self::assertEquals(AbstractSensorType::BOOL_READING_SENSOR_TYPE, $sensorReadingTypes[Motion::READING_TYPE]['sensorType']);
                self::assertEquals(Motion::READING_TYPE, $sensorReadingTypes[Motion::READING_TYPE]['readingType']);
            }
            if ($sensorObject->getSensorTypeObject()::getReadingTypeName() === GenericRelay::NAME) {
                $relayRepository = $this->entityManager->getRepository(Relay::class);
                /** @var Relay $relay */
                $relay = $relayRepository->find($singleSensorData['sensorReadingTypes'][Relay::READING_TYPE]['boolID']);
                self::assertEquals($relay->getBoolID(), $singleSensorData['sensorReadingTypes'][Relay::READING_TYPE]['boolID']);
                self::assertEquals($relay->getCurrentReading(), $singleSensorData['sensorReadingTypes'][Relay::READING_TYPE]['currentReading']);
                self::assertEquals($relay->getExpectedReading(), $singleSensorData['sensorReadingTypes'][Relay::READING_TYPE]['expectedReading']);
                self::assertEquals($relay->getRequestedReading(), $singleSensorData['sensorReadingTypes'][Relay::READING_TYPE]['requestedReading']);
                self::assertEquals($relay->getConstRecord(), $singleSensorData['sensorReadingTypes'][Relay::READING_TYPE]['constRecord']);
                self::assertEquals($relay->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Relay::READING_TYPE]['baseReadingTypeID']);
                self::assertEquals(AbstractSensorType::BOOL_READING_SENSOR_TYPE, $sensorReadingTypes[Relay::READING_TYPE]['sensorType']);
                self::assertEquals(Relay::READING_TYPE, $sensorReadingTypes[Relay::READING_TYPE]['readingType']);
            }

            self::assertEquals($sensorObject->getSensorID(), $singleSensorData['sensorID']);
            self::assertEquals($sensorObject->getSensorName(), $singleSensorData['sensorName']);

            $sensorTypeObject = $sensorObject->getSensorTypeObject();
            self::assertEquals($sensorTypeObject->getSensorTypeID(), $singleSensorData['sensorType']['sensorTypeID']);
            self::assertEquals($sensorTypeObject::getReadingTypeName(), $singleSensorData['sensorType']['sensorTypeName']);
            self::assertEquals($sensorTypeObject->getDescription(), $singleSensorData['sensorType']['sensorTypeDescription']);

            $deviceObject = $sensorObject->getDevice();
            self::assertEquals($deviceObject->getDeviceName(), $singleSensorData['device']['deviceName']);
            self::assertEquals($deviceObject->getDeviceID(), $singleSensorData['device']['deviceID']);

            $deviceRoom = $deviceObject->getRoomObject();
            self::assertEquals($deviceRoom->getRoomID(), $singleSensorData['device']['room']['roomID']);
            self::assertEquals($deviceRoom->getRoom(), $singleSensorData['device']['room']['roomName']);

            $deviceGroup = $sensorObject->getDevice()->getGroupObject();
            self::assertEquals($deviceGroup->getGroupID(), $singleSensorData['device']['group']['groupID']);
            self::assertEquals($deviceGroup->getGroupName(), $singleSensorData['device']['group']['groupName']);


            $user = $sensorObject->getCreatedBy();
            self::assertEquals($user->getEmail(), $singleSensorData['createdBy']['email']);
            self::assertEquals($user->getFirstName(), $singleSensorData['createdBy']['firstName']);
            self::assertEquals($user->getLastName(), $singleSensorData['createdBy']['lastName']);
            self::assertEquals($user->getUserID(), $singleSensorData['createdBy']['userID']);
            self::assertArrayNotHasKey('password', $singleSensorData['createdBy']);
            self::assertArrayNotHasKey('roles', $singleSensorData['createdBy']);

            self::assertTrue($singleSensorData['canEdit']);
            self::assertTrue($singleSensorData['canDelete']);

            self::assertEquals($sensorObject->getPinNumber(), $singleSensorData['pinNumber']);
            self::assertEquals($sensorObject->getReadingInterval(), $singleSensorData['readingInterval']);
            $userHasCardView = $this->cardViewRepository->findOneBy(
                [
                    'userID' => $this->adminUser->getUserID(),
                    'sensor' => $sensorObject->getSensorID()
                ]
            );

            if ($userHasCardView !== null) {
                $cardViewResponse = $singleSensorData['cardView'];
                self::assertEquals($userHasCardView->getCardViewID(), $cardViewResponse['cardViewID']);
                self::assertEquals($userHasCardView->getCardIconID()->getIconID(), $cardViewResponse['cardIcon']['iconID']);
                self::assertEquals($userHasCardView->getCardIconID()->getIconName(), $cardViewResponse['cardIcon']['iconName']);
                self::assertEquals($userHasCardView->getCardIconID()->getDescription(), $cardViewResponse['cardIcon']['description']);

                self::assertEquals($userHasCardView->getCardColourID()->getColourID(), $cardViewResponse['cardColour']['colourID']);
                self::assertEquals($userHasCardView->getCardColourID()->getColour(), $cardViewResponse['cardColour']['colour']);
                self::assertEquals($userHasCardView->getCardColourID()->getShade(), $cardViewResponse['cardColour']['shade']);

                self::assertEquals($userHasCardView->getCardStateID()->getStateID(), $cardViewResponse['cardViewState']['cardStateID']);
                self::assertEquals($userHasCardView->getCardStateID()->getState(), $cardViewResponse['cardViewState']['cardState']);
            }
        }
        self::assertGreaterThan(0, $sensorReadingTypePass);
    }

//    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_using_wrong_http_method(string $httpVerb): void
//    {
//        $this->client->request(
//            $httpVerb,
//            self::GET_ALL_SENSORS_URL,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
//        );
//
//        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
//    }

    public function wrongHttpsMethodDataProvider(): Generator
    {
        yield [Request::METHOD_POST];
        yield [Request::METHOD_PUT];
        yield [Request::METHOD_PATCH];
        yield [Request::METHOD_DELETE];
    }
}
