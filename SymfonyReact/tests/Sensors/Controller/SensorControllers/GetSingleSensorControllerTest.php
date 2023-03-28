<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Sensors\Controller\SensorControllers\GetSingleSensorsController;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Repository\Sensors\ORM\SensorTypeRepository;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\Repository\SensorType\ORM\GenericSensorTypeRepositoryInterface;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupNameRepository;
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

    private GroupNameRepository $groupNameRepository;

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
        $this->groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
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

        /** @var GroupNames[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        $groupNotApartOf = $groupsNotApartOf[0];
        $devicesUserIsNotApartOf = $this->deviceRepository->findOneBy([
            'groupNameID' => $groupNotApartOf->getGroupNameID(),
        ]);

        /** @var Sensor[] $sensorsNotOwnedByUser */
        $sensorsNotOwnedByUser = $this->sensorRepository->findBy([
            'deviceID' => $devicesUserIsNotApartOf->getDeviceID(),
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

        /** @var GroupNames[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        $groupNotApartOf = $groupsNotApartOf[0];
        $devicesUserIsNotApartOf = $this->deviceRepository->findOneBy([
            'groupNameID' => $groupNotApartOf->getGroupNameID(),
        ]);

        /** @var Sensor[] $sensorsNotOwnedByUser */
        $sensorsNotOwnedByUser = $this->sensorRepository->findBy([
            'deviceID' => $devicesUserIsNotApartOf->getDeviceID(),
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
    public function test_getting_all_sensor_types_responses(string $sensorType, array $allowedTypes): void
    {
        /** @var GenericSensorTypeRepositoryInterface $sensorTypeRepository */
        $sensorTypeRepository = $this->entityManager->getRepository($sensorType);

        /** @var SensorTypeInterface[] $sensorTypes */
        $sensorTypes = $sensorTypeRepository->findAll();

        $sensorType = $sensorTypes[0];

        $sensor = $sensorType->getSensor();
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $sensor->getSensorID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(GetSingleSensorsController::REQUEST_SUCCESSFUL, $responseData['title']);

        $payload = $responseData['payload'];
        self::assertCount(count($allowedTypes), $payload);

        foreach ($allowedTypes as $key => $type) {
            self::assertArrayHasKey($type, $payload[$key]);
        }
    }

    public function allSensorTypesDataProvider(): Generator
    {
        yield [
            'sensorType' => Dht::class,
            'allowedTypes' => [
                'temperature' => 'temperatureID',
                'humidity' => 'humidityID',
            ]
        ];

        yield [
            'sensorType' => Bmp::class,
            'allowedTypes' => [
                'temperature' => 'temperatureID',
                'humidity' => 'humidityID',
                'latitude' => 'latitudeID',
            ]
        ];

        yield [
            'sensorType' => Dallas::class,
            'allowedTypes' => [
                'temperature' => 'temperatureID',
            ]
        ];

        yield [
            'sensorType' => Soil::class,
            'allowedTypes' => [
                'analog' => 'analogID',
            ]
        ];
    }

    public function test_admin_user_can_get_sensor_device_group_not_apart_of(): void
    {
        /** @var User $adminUser */
        $adminUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);

        /** @var GroupNames[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($adminUser);

        while (true) {
            $randomGroup = $groupsNotApartOf[array_rand($groupsNotApartOf)];
            /** @var Devices $device */
            $device = $this->deviceRepository->findOneBy(['groupNameID' => $randomGroup]);
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

        /** @var GroupNames[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($regularUserTwo);

        while (true) {
            $randomGroup = $groupsNotApartOf[array_rand($groupsNotApartOf)];
            /** @var Devices $device */
            $device = $this->deviceRepository->findOneBy(['groupNameID' => $randomGroup]);
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

        /** @var GroupNames[] $groupsNotApartOf */
        $groupsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($regularUserTwo);

        while (true) {
            $randomGroup = $groupsApartOf[array_rand($groupsApartOf)];
            /** @var Devices $device */
            $device = $this->deviceRepository->findOneBy(['groupNameID' => $randomGroup]);
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

        /** @var GroupNames[] $groupsNotApartOf */
        $groupsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($adminUser);

        while (true) {
            $randomGroup = $groupsApartOf[array_rand($groupsApartOf)];
            /** @var Devices $device */
            $device = $this->deviceRepository->findOneBy(['groupNameID' => $randomGroup]);
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
     * @dataProvider fullResponseSensorDataProvider
     */
    public function test_get_full_response_data(string $sensorType, array $expectedResponseTypes, array $notExpectedResponseTypes): void
    {
        /** @var SensorType $sensorTypeObject */
        $sensorTypeObject = $this->sensorTypeRepository->findOneBy(['sensorType' => $sensorType]);
        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findBy(['sensorTypeID' => $sensorTypeObject]);

        $sensor = $sensors[array_rand($sensors)];
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGULAR_SENSOR_URL, $sensor->getSensorID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals(GetSingleSensorsController::REQUEST_SUCCESSFUL, $responseData['title']);

        $payload = $responseData['payload'];

        self::assertCount(count($expectedResponseTypes), $payload);

        foreach ($payload as $responseType) {
            foreach ($notExpectedResponseTypes as $notExpectedResponseType) {
                self::assertArrayNotHasKey($notExpectedResponseType, $responseType);
            }
        }
    }

    public function fullResponseSensorDataProvider(): Generator
    {
        yield [
            'sensorType' => Dht::NAME,
            'expectedResponseTypes' => [
                'temperatureID',
                'humidityID',
            ],
            'notExpectedResponseTypes' => [
                'latitudeID',
                'analogID'
            ]
        ];

        yield [
            'sensorType' => Soil::NAME,
            'expectedResponseTypes' => [
                'analogID',
            ],
            'notExpectedResponseTypes' => [
                'latitudeID',
                'temperatureID',
                'humidityID'
            ]
        ];

        yield [
            'sensorType' => Dallas::NAME,
            'expectedResponseTypes' => [
                'temperatureID',
            ],
            'notExpectedResponseTypes' => [
                'latitudeID',
                'analogID',
                'humidityID'
            ]
        ];

        yield [
            'sensorType' => Bmp::NAME,
            'expectedResponseTypes' => [
                'temperatureID',
                'humidityID',
                'latitudeID',
            ],
            'notExpectedResponseTypes' => [
                'analogID',
            ]
        ];
    }
}
