<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Sensors\Controller\SensorControllers\GetSingleSensorsController;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Repository\Sensors\ORM\SensorTypeRepository;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\Repository\SensorType\ORM\GenericSensorTypeRepositoryInterface;
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

class GetSingleSensorControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_SINGULAR_SENSOR_URL = '/HomeApp/api/user/sensor/%d/get';

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
        $this->sensorTypeRepository = $this->entityManager->getRepository(AbstractSensorType::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findAll();
        $sensor = $sensors[0];

        $this->client->request(
            $httpVerb,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $sensor->getSensorID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_POST],
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }

    public function test_getting_sensor_group_not_apart_of_regular_user(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);

        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        $devicesUserIsNotApartOf = $this->deviceRepository->findBy([
            'groupID' => $groupsNotApartOf,
        ]);

        $device = $devicesUserIsNotApartOf[0];

        /** @var Sensor[] $sensorsNotOwnedByUser */
        $sensorsNotOwnedByUser = $this->sensorRepository->findBy([
            'deviceID' => $device->getDeviceID(),
        ]);

        $sensorNotOwnedByUser = $sensorsNotOwnedByUser[0];

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $sensorNotOwnedByUser->getSensorID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function test_getting_sensor_group_not_apart_of_admin_user(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);

        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        $devicesUserIsNotApartOf = $this->deviceRepository->findBy([
            'groupID' => $groupsNotApartOf,
        ]);

        $device = $devicesUserIsNotApartOf[0];

        /** @var Sensor[] $sensorsNotOwnedByUser */
        $sensorsNotOwnedByUser = $this->sensorRepository->findBy([
            'deviceID' => $device->getDeviceID(),
        ]);

        $sensorNotOwnedByUser = $sensorsNotOwnedByUser[0];
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $sensorNotOwnedByUser->getSensorID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(GetSingleSensorsController::REQUEST_SUCCESSFUL, $responseData['title']);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);
    }

    /**
     * @dataProvider allSensorTypesDataProvider
     */
    public function test_getting_all_sensor_response_admin(string $sensorType, array $allowedTypes): void
    {
        /** @var GenericSensorTypeRepositoryInterface $sensorTypeRepository */
        $sensorType = $this->entityManager->getRepository($sensorType)->findAll()[0];

        $sensor = $this->sensorRepository->findBy(['sensorTypeID' => $sensorType])[0];
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $sensor->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(GetSingleSensorsController::REQUEST_SUCCESSFUL, $responseData['title']);

        $payload = $responseData['payload'];
        self::assertCount(count($allowedTypes), $payload['sensorReadingTypes']);

        foreach ($allowedTypes as $type) {
            self::assertArrayHasKey($type, $payload['sensorReadingTypes']);
        }
    }

    public function allSensorTypesDataProvider(): Generator
    {
        yield [
            'sensorType' => Dht::class,
            'allowedTypes' => [
                Temperature::READING_TYPE => Temperature::READING_TYPE,
                Humidity::READING_TYPE => Humidity::READING_TYPE,
            ]
        ];

        yield [
            'sensorType' => Bmp::class,
            'allowedTypes' => [
                Temperature::READING_TYPE => Temperature::READING_TYPE,
                Humidity::READING_TYPE => Humidity::READING_TYPE,
                Latitude::READING_TYPE => Latitude::READING_TYPE,
            ]
        ];

        yield [
            'sensorType' => Dallas::class,
            'allowedTypes' => [
                Temperature::READING_TYPE => Temperature::READING_TYPE,
            ]
        ];

        yield [
            'sensorType' => Soil::class,
            'allowedTypes' => [
                Analog::READING_TYPE => Analog::READING_TYPE,
            ]
        ];

        yield [
            'sensorType' => GenericMotion::class,
            'allowedTypes' => [
                Motion::READING_TYPE => Motion::READING_TYPE,
            ]
        ];

        yield [
            'sensorType' => GenericRelay::class,
            'allowedTypes' => [
                Relay::READING_TYPE => Relay::READING_TYPE,
            ]
        ];

        yield [
            'sensorType' => LDR::class,
            'allowedTypes' => [
                Analog::READING_TYPE => Analog::READING_TYPE,
            ]
        ];

        yield [
            'sensorType' => Sht::class,
            'allowedTypes' => [
                Temperature::READING_TYPE => Temperature::READING_TYPE,
                Humidity::READING_TYPE => Humidity::READING_TYPE,
            ]
        ];
    }

    public function test_admin_user_can_get_sensor_device_group_not_apart_of(): void
    {
        /** @var User $adminUser */
        $adminUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);

        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($adminUser);

        while (true) {
            $randomGroup = $groupsNotApartOf[array_rand($groupsNotApartOf)];
            /** @var Devices $device */
            $device = $this->deviceRepository->findOneBy(['groupID' => $randomGroup]);
            if ($device) {
                break;
            }
        }

        /** @var Sensor[] $deviceSensors */
        $deviceSensors = $this->sensorRepository->findBy(['deviceID' => $device]);
        $deviceSensor = $deviceSensors[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $deviceSensor->getSensorID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(GetSingleSensorsController::REQUEST_SUCCESSFUL, $responseData['title']);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);
    }

    public function test_regular_user_cannot_get_sensor_device_group_not_apart_of(): void
    {
        /** @var User $regularUserTwo */
        $regularUserTwo = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);

        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($regularUserTwo);

        while (true) {
            $randomGroup = $groupsNotApartOf[array_rand($groupsNotApartOf)];
            /** @var Devices $device */
            $device = $this->deviceRepository->findOneBy(['groupID' => $randomGroup]);
            if ($device) {
                break;
            }
        }

        /** @var Sensor[] $deviceSensors */
        $deviceSensors = $this->sensorRepository->findBy(['deviceID' => $device]);
        $deviceSensor = $deviceSensors[0];

        $userToken = $this->setUserToken($this->client, $regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $deviceSensor->getSensorID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(GetSingleSensorsController::NOT_AUTHORIZED_TO_BE_HERE, $responseData['title']);
        self::assertEquals([APIErrorMessages::ACCESS_DENIED], $responseData['errors']);

    }

    public function test_regular_user_can_get_sensor_device_group_apart_of(): void
    {
        /** @var User $regularUserTwo */
        $regularUserTwo = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);

        /** @var Group[] $groupsNotApartOf */
        $groupsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($regularUserTwo);

        while (true) {
            $randomGroup = $groupsApartOf[array_rand($groupsApartOf)];
            /** @var Devices $device */
            $device = $this->deviceRepository->findOneBy(['groupID' => $randomGroup]);
            if ($device) {
                break;
            }
        }

        /** @var Sensor[] $deviceSensors */
        $deviceSensors = $this->sensorRepository->findBy(['deviceID' => $device]);
        $deviceSensor = $deviceSensors[0];

        $userToken = $this->setUserToken($this->client, $regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $deviceSensor->getSensorID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(GetSingleSensorsController::REQUEST_SUCCESSFUL, $responseData['title']);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

    }

    public function test_admin_user_can_get_sensor_device_group_apart_of(): void
    {
        /** @var User $adminUser */
        $adminUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);

        /** @var Group[] $groupsNotApartOf */
        $groupsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($adminUser);

        while (true) {
            $randomGroup = $groupsApartOf[array_rand($groupsApartOf)];
            /** @var Devices $device */
            $device = $this->deviceRepository->findOneBy(['groupID' => $randomGroup]);
            if ($device) {
                break;
            }
        }

        /** @var Sensor[] $deviceSensors */
        $deviceSensors = $this->sensorRepository->findBy(['deviceID' => $device]);
        $deviceSensor = $deviceSensors[0];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $deviceSensor->getSensorID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(GetSingleSensorsController::REQUEST_SUCCESSFUL, $responseData['title']);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

    }

    /**
     * @dataProvider filterResponseSensorDataProvider
     */
    public function test_get_filtered_response_data(
        string $sensorType,
        array $expectedResponseTypes,
        array $notExpectedResponseTypes
    ): void {
        /** @var AbstractSensorType $sensorTypeObject */
        $sensorTypeObject = $this->entityManager->getRepository($sensorType)->findAll()[0];
        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findBy(['sensorTypeID' => $sensorTypeObject]);

        $sensor = $sensors[array_rand($sensors)];
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $sensor->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(GetSingleSensorsController::REQUEST_SUCCESSFUL, $responseData['title']);

        $payload = $responseData['payload'];

        self::assertCount(count($expectedResponseTypes), $payload['sensorReadingTypes']);

        foreach ($payload['sensorReadingTypes'] as $responseType) {
            foreach ($notExpectedResponseTypes as $notExpectedResponseType) {
                self::assertArrayNotHasKey($notExpectedResponseType, $responseType);
            }
        }
    }

    public function filterResponseSensorDataProvider(): Generator
    {
        yield [
            'sensorType' => Dht::class,
            'expectedResponseTypes' => [
                'temperatureID',
                'humidityID',
            ],
            'notExpectedResponseTypes' => [
                'latitudeID',
                'analogID',
                'boolID',
            ]
        ];

        yield [
            'sensorType' => Soil::class,
            'expectedResponseTypes' => [
                'analogID',
            ],
            'notExpectedResponseTypes' => [
                'latitudeID',
                'temperatureID',
                'humidityID',
                'boolID',
            ]
        ];

        yield [
            'sensorType' => Dallas::class,
            'expectedResponseTypes' => [
                'temperatureID',
            ],
            'notExpectedResponseTypes' => [
                'latitudeID',
                'analogID',
                'humidityID',
                'boolID',
            ]
        ];

        yield [
            'sensorType' => Bmp::class,
            'expectedResponseTypes' => [
                'temperatureID',
                'humidityID',
                'latitudeID',
            ],
            'notExpectedResponseTypes' => [
                'analogID',
                'boolID',
            ]
        ];

        yield [
            'sensorType' => GenericMotion::class,
            'expectedResponseTypes' => [
                'boolID',
            ],
            'notExpectedResponseTypes' => [
                'latitudeID',
                'analogID',
                'humidityID',
                'temperatureID',
            ]
        ];

        yield [
            'sensorType' => GenericRelay::class,
            'expectedResponseTypes' => [
                'boolID',
            ],
            'notExpectedResponseTypes' => [
                'latitudeID',
                'analogID',
                'humidityID',
                'temperatureID',
            ]
        ];

        yield [
            'sensorType' => LDR::class,
            'expectedResponseTypes' => [
                'analogID',
            ],
            'notExpectedResponseTypes' => [
                'latitudeID',
                'humidityID',
                'temperatureID',
                'boolID',
            ]
        ];

        yield [
            'sensorType' => Sht::class,
            'expectedResponseTypes' => [
                'temperatureID',
                'humidityID',
            ],
            'notExpectedResponseTypes' => [
                'latitudeID',
                'analogID',
                'boolID',
            ]
        ];
    }

    public function test_response_admin_full(): void
    {
        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findAll();

        $sensorObject = $sensors[array_rand($sensors)];
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $sensorObject->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(GetSingleSensorsController::REQUEST_SUCCESSFUL, $responseData['title']);

        self::assertNotEmpty($responseData['payload']);
        $sensorData = $responseData['payload'];
        $sensorReadingTypes = $sensorData['sensorReadingTypes'];

        self::assertEquals($sensorObject->getPinNumber(), $sensorData['pinNumber']);
        self::assertEquals($sensorObject->getReadingInterval(), $sensorData['readingInterval']);
        if (
            $sensorObject->getSensorTypeObject()::getReadingTypeName() === Dht::NAME
            || $sensorObject->getSensorTypeObject()::getReadingTypeName() === Dallas::NAME
            || $sensorObject->getSensorTypeObject()::getReadingTypeName() === Bmp::NAME
        ) {
            $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
            /** @var Temperature $temperature */
            $temperature = $temperatureRepository->find($sensorReadingTypes[Temperature::READING_TYPE]['temperatureID']);
            self::assertEquals($temperature->getSensorID(), $sensorReadingTypes[Temperature::READING_TYPE]['temperatureID']);
            self::assertEquals($temperature->getCurrentReading(), $sensorReadingTypes[Temperature::READING_TYPE]['currentReading']);
            self::assertEquals($temperature->getHighReading(), $sensorReadingTypes[Temperature::READING_TYPE]['highReading']);
            self::assertEquals($temperature->getLowReading(), $sensorReadingTypes[Temperature::READING_TYPE]['lowReading']);
            self::assertEquals($temperature->getConstRecord(), $sensorReadingTypes[Temperature::READING_TYPE]['constRecord']);
            self::assertEquals($temperature->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Temperature::READING_TYPE]['baseReadingTypeID']);
        }
        if (
            $sensorObject->getSensorTypeObject()::getReadingTypeName() === Dht::NAME
            || $sensorObject->getSensorTypeObject()::getReadingTypeName() === Bmp::NAME
        ) {
            $humidityRepository = $this->entityManager->getRepository(Humidity::class);
            /** @var Humidity $humidity */
            $humidity = $humidityRepository->find($sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['humidityID']);
            self::assertEquals($humidity->getSensorID(), $sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['humidityID']);
            self::assertEquals($humidity->getCurrentReading(), $sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['currentReading']);
            self::assertEquals($humidity->getHighReading(), $sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['highReading']);
            self::assertEquals($humidity->getLowReading(), $sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['lowReading']);
            self::assertEquals($humidity->getConstRecord(), $sensorData['sensorReadingTypes'][Humidity::READING_TYPE]['constRecord']);
            self::assertEquals($humidity->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Humidity::READING_TYPE]['baseReadingTypeID']);
        }

        if ($sensorObject->getSensorTypeObject()::getReadingTypeName() === Bmp::NAME) {
            $latitudeRepository = $this->entityManager->getRepository(Latitude::class);
            /** @var Latitude $latitude */
            $latitude = $latitudeRepository->find($sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['latitudeID']);
            self::assertEquals($latitude->getSensorID(), $sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['latitudeID']);
            self::assertEquals($latitude->getCurrentReading(), $sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['currentReading']);
            self::assertEquals($latitude->getHighReading(), $sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['highReading']);
            self::assertEquals($latitude->getLowReading(), $sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['lowReading']);
            self::assertEquals($latitude->getConstRecord(), $sensorData['sensorReadingTypes'][Latitude::READING_TYPE]['constRecord']);
            self::assertEquals($latitude->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Latitude::READING_TYPE]['baseReadingTypeID']);
        }

        if (
            $sensorObject->getSensorTypeObject()::getReadingTypeName() === Soil::NAME
            || $sensorObject->getSensorTypeObject()::getReadingTypeName() === LDR::NAME
        ) {
            $analogRepository = $this->entityManager->getRepository(Analog::class);
            /** @var Analog $analog */
            $analog = $analogRepository->find($sensorData['sensorReadingTypes'][Analog::READING_TYPE]['analogID']);
            self::assertEquals($analog->getSensorID(), $sensorData['sensorReadingTypes'][Analog::READING_TYPE]['analogID']);
            self::assertEquals($analog->getCurrentReading(), $sensorData['sensorReadingTypes'][Analog::READING_TYPE]['currentReading']);
            self::assertEquals($analog->getHighReading(), $sensorData['sensorReadingTypes'][Analog::READING_TYPE]['highReading']);
            self::assertEquals($analog->getLowReading(), $sensorData['sensorReadingTypes'][Analog::READING_TYPE]['lowReading']);
            self::assertEquals($analog->getConstRecord(), $sensorData['sensorReadingTypes'][Analog::READING_TYPE]['constRecord']);
            self::assertEquals($analog->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Analog::READING_TYPE]['baseReadingTypeID']);
        }

        if ($sensorObject->getSensorTypeObject()::getReadingTypeName() === GenericMotion::NAME) {
            $motionRepository = $this->entityManager->getRepository(Motion::class);
            /** @var Motion $motion */
            $motion = $motionRepository->find($sensorData['sensorReadingTypes'][Motion::READING_TYPE]['boolID']);
            self::assertEquals($motion->getBoolID(), $sensorData['sensorReadingTypes'][Motion::READING_TYPE]['boolID']);
            self::assertEquals($motion->getCurrentReading(), $sensorData['sensorReadingTypes'][Motion::READING_TYPE]['currentReading']);
            self::assertEquals($motion->getExpectedReading(), $sensorData['sensorReadingTypes'][Motion::READING_TYPE]['expectedReading']);
            self::assertEquals($motion->getRequestedReading(), $sensorData['sensorReadingTypes'][Motion::READING_TYPE]['requestedReading']);
            self::assertEquals($motion->getConstRecord(), $sensorData['sensorReadingTypes'][Motion::READING_TYPE]['constRecord']);
            self::assertEquals(AbstractSensorType::BOOL_READING_SENSOR_TYPE, $sensorReadingTypes[Motion::READING_TYPE]['sensorType']);
            self::assertEquals(Motion::READING_TYPE, $sensorReadingTypes[Motion::READING_TYPE]['readingType']);
            self::assertEquals($motion->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Motion::READING_TYPE]['baseReadingTypeID']);
        }
        if ($sensorObject->getSensorTypeObject()::getReadingTypeName() === GenericRelay::NAME) {
            $relayRepository = $this->entityManager->getRepository(Relay::class);
            /** @var Relay $relay */
            $relay = $relayRepository->find($sensorData['sensorReadingTypes'][Relay::READING_TYPE]['boolID']);
            self::assertEquals($relay->getBoolID(), $sensorData['sensorReadingTypes'][Relay::READING_TYPE]['boolID']);
            self::assertEquals($relay->getCurrentReading(), $sensorData['sensorReadingTypes'][Relay::READING_TYPE]['currentReading']);
            self::assertEquals($relay->getExpectedReading(), $sensorData['sensorReadingTypes'][Relay::READING_TYPE]['expectedReading']);
            self::assertEquals($relay->getRequestedReading(), $sensorData['sensorReadingTypes'][Relay::READING_TYPE]['requestedReading']);
            self::assertEquals($relay->getConstRecord(), $sensorData['sensorReadingTypes'][Relay::READING_TYPE]['constRecord']);
            self::assertEquals(AbstractSensorType::BOOL_READING_SENSOR_TYPE, $sensorReadingTypes[Relay::READING_TYPE]['sensorType']);
            self::assertEquals(Relay::READING_TYPE, $sensorReadingTypes[Relay::READING_TYPE]['readingType']);
            self::assertEquals($relay->getBaseReadingType()->getBaseReadingTypeID(), $sensorReadingTypes[Relay::READING_TYPE]['baseReadingTypeID']);
        }

        self::assertEquals($sensorObject->getSensorID(), $sensorData['sensorID']);
        self::assertEquals($sensorObject->getSensorName(), $sensorData['sensorName']);
        self::assertEquals($sensorObject->getPinNumber(), $sensorData['pinNumber']);
        self::assertEquals($sensorObject->getReadingInterval(), $sensorData['readingInterval']);

        $sensorTypeObject = $sensorObject->getSensorTypeObject();
        self::assertEquals($sensorTypeObject->getSensorTypeID(), $sensorData['sensorType']['sensorTypeID']);
        self::assertEquals($sensorTypeObject::getReadingTypeName(), $sensorData['sensorType']['sensorTypeName']);
        self::assertEquals($sensorTypeObject->getDescription(), $sensorData['sensorType']['sensorTypeDescription']);

        $deviceObject = $sensorObject->getDevice();
        self::assertEquals($deviceObject->getDeviceName(), $sensorData['device']['deviceName']);
        self::assertEquals($deviceObject->getDeviceID(), $sensorData['device']['deviceID']);

        $deviceRoom = $deviceObject->getRoomObject();
        self::assertEquals($deviceRoom->getRoomID(), $sensorData['device']['room']['roomID']);
        self::assertEquals($deviceRoom->getRoom(), $sensorData['device']['room']['roomName']);

        $deviceGroup = $sensorObject->getDevice()->getGroupObject();
        self::assertEquals($deviceGroup->getGroupID(), $sensorData['device']['group']['groupID']);
        self::assertEquals($deviceGroup->getGroupName(), $sensorData['device']['group']['groupName']);


        $user = $sensorObject->getCreatedBy();
        self::assertEquals($user->getEmail(), $sensorData['createdBy']['email']);
        self::assertEquals($user->getFirstName(), $sensorData['createdBy']['firstName']);
        self::assertEquals($user->getLastName(), $sensorData['createdBy']['lastName']);
        self::assertEquals($user->getUserID(), $sensorData['createdBy']['userID']);
        self::assertArrayNotHasKey('password', $sensorData['createdBy']);
        self::assertArrayNotHasKey('roles', $sensorData['createdBy']);
        self::assertTrue($sensorData['canEdit']);
        self::assertTrue($sensorData['canDelete']);
    }

    public function test_response_regular_full(): void
    {

    }

    public function test_response_admin_only(): void
    {

    }

    public function test_response_regular_only(): void
    {

    }
}
