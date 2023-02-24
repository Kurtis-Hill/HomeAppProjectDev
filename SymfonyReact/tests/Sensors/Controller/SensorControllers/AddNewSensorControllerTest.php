<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Authentication\Controller\SecurityController;
use App\Common\API\APIErrorMessages;
use App\Common\API\HTTPStatusCodes;
use App\Devices\Entity\Devices;
use App\Sensors\Controller\SensorControllers\AddNewSensorController;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupNameRepository;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddNewSensorControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_NEW_SENSOR_URL = '/HomeApp/api/user/sensors/add-new-sensor';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?Devices $device;

    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name']]);
            $this->userToken = $this->setUserToken($this->client);
        } catch (JsonException $e) {
            error_log($e);
        }
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function newSensorSimpleDataProvider(): Generator
    {
        yield [
            'sensor' => Dht::NAME,
            'sensorName' => 'dhtTest'
        ];

        yield [
            'sensor' => Bmp::NAME,
            'sensorName' => 'bmpTest'
        ];

        yield [
            'sensor' => Soil::NAME,
            'sensorName' => 'soilTest'
        ];

        yield [
            'sensor' => Dallas::NAME,
            'sensorName' => 'dallasTest'
        ];
    }

    public function newSensorExtendedDataProvider(): Generator
    {
        yield [
            'sensor' => Dht::NAME,
            'sensorName' => 'dhtTest',
            'class' => Dht::class,
            [
                'temperature' => Temperature::class,
                'humidity' => Humidity::class,
            ]
        ];

        yield [
            'sensor' => Bmp::NAME,
            'sensorName' => 'bmpTest',
            'class' => Bmp::class,
            [
                'temperature' => Temperature::class,
                'humidity' => Humidity::class,
                'latitude' => Latitude::class
            ]
        ];

        yield [
            'sensor' => Soil::NAME,
            'sensorName' => 'soilTest',
            'class' => Soil::class,
            [
                'analog' => Analog::class
            ]
        ];

        yield [
            'sensor' => Dallas::NAME,
            'sensorName' => 'dallasTest',
            'class' => Dallas::class,
            [
                'temperature' => Temperature::class,
            ]
        ];
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     */
    public function test_can_add_new_sensor_correct_details(string $sensorType, string $sensorName): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceID(),
        ];

        $jsonData = json_encode($formData);
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $sensorID = $responseData['payload']['sensorNameID'];

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorID' => $sensorID]);

        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_CREATED);
        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);

        self::assertEquals($responseData['payload']['sensorNameID'], $sensor->getSensorID());
        self::assertEquals($responseData['payload']['sensorName'], $sensor->getSensorName());
        self::assertEquals($responseData['payload']['sensorType'], $sensor->getSensorTypeObject()->getSensorType());
        self::assertEquals($responseData['payload']['deviceName'], $sensor->getDevice()->getDeviceName());
        self::assertEquals($responseData['payload']['createdBy'], $sensor->getCreatedBy()->getUserIdentifier());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     * @param string $sensorType
     * @param string $sensorName
     */
    public function test_can_not_add_new_sensor_with_special_characters(string $sensorType, string $sensorName): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensorName' => '&' . $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($sensor);
        self::assertStringContainsString('The name cannot contain any special characters, please choose a different name', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     * @param string $sensorType
     * @param string $sensorName
     */
    public function test_can_not_add_new_sensor_with_long_name(string $sensorType, string $sensorName): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensorName' => 'TestingTestingTestingTestingTestingTestingTesting' . $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($sensor);
        self::assertStringContainsString("Sensor name cannot be longer than 50 characters", $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     */
    public function test_can_not_add_new_sensor_with_short_name(string $sensorType): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensorName' => 'T',
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($sensor);
        self::assertStringContainsString('Sensor name must be at least 2 characters long', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     */
    public function test_cannot_add_new_sensor_with_identical_name(string $sensorType): void
    {
        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceAdminGroupOne']['referenceName']]);
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findBy(['deviceID' => $device->getDeviceID()])[0];

        $formData = [
            'sensorName' => $sensor->getSensorName(),
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString(
            sprintf(
                DuplicateSensorException::MESSAGE,
                $sensor->getSensorName(),
            ),
            $responseData['errors'][0]
        );
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     * @param string $sensorType
     * @param string $sensorName
     */
    public function test_cannot_add_new_sensor_with_none_existant_device_id(string $sensorType, string $sensorName): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        while (true) {
            $randomID = random_int(0, 1000000);
            $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $randomID]);
            if (!$device instanceof Devices) {
                break;
            }
        }

        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $randomID,
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);

        self::assertNull($sensor);
        self::assertStringContainsString('Device not found', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_new_sensor_with_none_existant_sensor_type(): void
    {
        while (true) {
            $randomID = random_int(0, 1000000);
            $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $randomID]);
            if (!$sensorType instanceof SensorType) {
                break;
            }
        }

        $formData = [
            'sensorName' => 'testing',
            'sensorTypeID' => $randomID,
            'deviceNameID' => $this->device->getDeviceID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);

        self::assertNull($sensor);
        self::assertStringContainsString('SensorType not found', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorExtendedDataProvider
     */
    public function test_can_add_sensor_and_card_details_admin(string $sensorType, string $sensorName, string $class, array $sensors): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $sensorID = $responseData['payload']['sensorNameID'];

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorID' => $sensorID]);
        /** @var SensorTypeInterface $sensorTypeObject */
        $sensorTypeObject = $this->entityManager->getRepository($class)->findOneBy(['sensor' => $sensorID]);
        /** @var CardView $cardView */
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensor' => $sensorID]);

        foreach ($sensors as $sensorTypeClass) {
            $sensorType = $this->entityManager->getRepository($sensorTypeClass)->findOneBy(['sensor' => $sensorID]);
            self::assertInstanceOf($sensorTypeClass, $sensorType);
        }

        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertInstanceOf($class, $sensorTypeObject);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);

        self::assertEquals($responseData['payload']['sensorNameID'], $sensor->getSensorID());
        self::assertEquals($responseData['payload']['sensorName'], $sensor->getSensorName());
        self::assertEquals($responseData['payload']['sensorType'], $sensor->getSensorTypeObject()->getSensorType());
        self::assertEquals($responseData['payload']['deviceName'], $sensor->getDevice()->getDeviceName());
        self::assertEquals($responseData['payload']['createdBy'], $sensor->getCreatedBy()->getUserIdentifier());
    }

    /**
     * @dataProvider newSensorExtendedDataProvider
     */
    public function test_can_add_sensor_and_card_details_admin_group_not_apart_of(string $sensorType, string $sensorName, string $class, array $sensors): void
    {
//        /** @var AllSensorReadingTypeInterface $sensorType */
        /** @var SensorType $sensorTypeMappingObject */
        $sensorTypeMappingObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);
        /** @var GroupNameRepository $groupNameRepository */
        $groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
        /** @var GroupNames[] $groupsNotApartOf */
        $groupsNotApartOf = $groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupNameIds(),
        );


        $counter = 0;
        foreach ($groupsNotApartOf as $group) {
            /** @var Devices[] $devices */
            $devices = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $group->getGroupNameID()]);

            foreach ($devices as $device) {
                $formData = [
                    'sensorName' => $sensorName . $counter,
                    'sensorTypeID' => $sensorTypeMappingObject->getSensorTypeID(),
                    'deviceNameID' => $device->getDeviceID(),
                ];

                $jsonData = json_encode($formData);

                $this->client->request(
                    Request::METHOD_POST,
                    self::ADD_NEW_SENSOR_URL,
                    [],
                    [],
                    ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
                    $jsonData
                );

                $responseData = json_decode($this->client->getResponse()->getContent(), true);
                $payload = $responseData['payload'] ?? [];

                if (empty($payload)) {
                    self::fail('Payload is empty');
                }
                $sensorID = $payload['sensorNameID'];

                /** @var Sensor $sensor */
                $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorID' => $sensorID]);
                /** @var SensorTypeInterface $sensorTypeObject */
                $sensorTypeObject = $this->entityManager->getRepository($class)->findOneBy(['sensor' => $sensorID]);
                /** @var CardView $cardView */
                $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensor' => $sensorID]);

                foreach ($sensors as $sensorTypeClass) {
                    /** @var SensorTypeInterface $sensorType */
                    $sensorType = $this->entityManager->getRepository($sensorTypeClass)->findOneBy(['sensor' => $sensorID]);
                    self::assertInstanceOf($sensorTypeClass, $sensorType);
                }

                self::assertInstanceOf(Sensor::class, $sensor);
                self::assertInstanceOf($class, $sensorTypeObject);
                self::assertInstanceOf(CardView::class, $cardView);

                self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_CREATED);
                self::assertInstanceOf(Sensor::class, $sensor);
                self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);

                self::assertEquals($responseData['payload']['sensorNameID'], $sensor->getSensorID());
                self::assertEquals($responseData['payload']['sensorName'], $sensor->getSensorName());
                self::assertEquals($responseData['payload']['sensorType'], $sensor->getSensorTypeObject()->getSensorType());
                self::assertEquals($responseData['payload']['deviceName'], $sensor->getDevice()->getDeviceName());
                self::assertEquals($responseData['payload']['createdBy'], $sensor->getCreatedBy()->getUserIdentifier());
                ++$counter;
            }
        }
    }

    /**
     * @dataProvider newSensorExtendedDataProvider
     */
    public function test_can_add_sensor_and_card_details_regular_user_admin_group_is_apart_of(string $sensorType, string $sensorName, string $class, array $sensors): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $sensorID = $responseData['payload']['sensorNameID'];

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorID' => $sensorID]);
        /** @var SensorTypeInterface $sensorTypeObject */
        $sensorTypeObject = $this->entityManager->getRepository($class)->findOneBy(['sensor' => $sensorID]);
        /** @var CardView $cardView */
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensor' => $sensorID]);

        foreach ($sensors as $sensorTypeClass) {
            /** @var AllSensorReadingTypeInterface $sensorType */
            $sensorType = $this->entityManager->getRepository($sensorTypeClass)->findOneBy(['sensor' => $sensorID]);
            self::assertInstanceOf($sensorTypeClass, $sensorType);
        }

        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertInstanceOf($class, $sensorTypeObject);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);
        self::assertEquals($responseData['payload']['sensorNameID'], $sensor->getSensorID());
        self::assertEquals($responseData['payload']['sensorName'], $sensor->getSensorName());
        self::assertEquals($responseData['payload']['sensorType'], $sensor->getSensorTypeObject()->getSensorType());
        self::assertEquals($responseData['payload']['deviceName'], $sensor->getDevice()->getDeviceName());
        self::assertEquals($responseData['payload']['createdBy'], $sensor->getCreatedBy()->getUserIdentifier());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     */
    public function test_add_new_sensor_when_not_part_of_associated_device_group_regular_user(string $sensorType, string $sensorName): void
    {
        $token = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        /** @var GroupNameRepository $groupRepository */
        $groupRepository = $this->entityManager->getRepository(GroupNames::class);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        /** @var GroupNames[] $groupNames */
        $groupNames = $groupRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupNameIds(),
        );

        if (empty($groupNames)) {
            self::fail('No groups found for user to add sensor to');
        }
        foreach ($groupNames as $groupName) {
            /** @var Devices[] $devices */
            $devices = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupName->getGroupNameID()]);

            foreach ($devices as $device) {
                $formData = [
                    'sensorName' => $sensorName,
                    'sensorTypeID' => $sensorType->getSensorTypeID(),
                    'deviceNameID' => $device->getDeviceID(),
                ];

                $jsonData = json_encode($formData);

                $this->client->request(
                    Request::METHOD_POST,
                    self::ADD_NEW_SENSOR_URL,
                    [],
                    [],
                    ['HTTP_AUTHORIZATION' => 'BEARER ' . $token],
                    $jsonData,
                );

                $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

                self::assertStringContainsString('You Are Not Authorised To Be Here', $responseData['title']);
                self::assertStringContainsString(APIErrorMessages::ACCESS_DENIED, $responseData['errors'][0]);
                self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_FORBIDDEN);
            }
        }
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::ADD_NEW_SENSOR_URL,
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
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }
}
