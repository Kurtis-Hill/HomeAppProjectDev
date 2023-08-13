<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\HTTPStatusCodes;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Sensors\Controller\SensorControllers\AddNewSensorController;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\BoolSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
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

    private const ADD_NEW_SENSOR_URL = '/HomeApp/api/user/sensor/add';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?Devices $device;

    private DeviceRepository $deviceRepository;

    private SensorRepositoryInterface $sensorRepository;

    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);

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

        yield [
            'sensor' => GenericMotion::NAME,
            'sensorName' => 'genericMotionTest'
        ];

        yield [
            'sensor' => GenericRelay::NAME,
            'sensorName' => 'genericRelayTest'
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

        yield [
            'sensor' => GenericMotion::NAME,
            'sensorName' => 'genericMotionTest',
            'class' => GenericMotion::class,
            [
                'motion' => Motion::class
            ]
        ];

        yield [
            'sensor' => GenericRelay::NAME,
            'sensorName' => 'genericRelayTest',
            'class' => GenericRelay::class,
            [
                'relay' => Relay::class
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

        $devicePinsInUse = $this->deviceRepository->findAllDevicePinsInUse($this->device->getDeviceID());

        while (true) {
            $randomPin = random_int(0, 10);
            if (!in_array($randomPin, $devicePinsInUse)) {
                break;
            }
        }

        $readingInterval = 1000;
        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => $randomPin,
            'readingInterval' => $readingInterval,
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
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_CREATED);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $sensorID = $responseData['payload']['sensorID'];

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorID' => $sensorID]);

        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);

        self::assertEquals($sensorName, $sensor->getSensorName());
        self::assertEquals($randomPin, $sensor->getPinNumber());
        self::assertEquals($readingInterval, $sensor->getReadingInterval());

        self::assertEquals($responseData['payload']['sensorID'], $sensor->getSensorID());
        self::assertEquals($responseData['payload']['sensorName'], $sensor->getSensorName());
        self::assertEquals($responseData['payload']['pinNumber'], $sensor->getPinNumber());
        self::assertEquals($responseData['payload']['readingInterval'], $sensor->getReadingInterval());
        self::assertEquals($responseData['payload']['sensorType']['sensorTypeName'], $sensor->getSensorTypeObject()->getSensorType());
        self::assertEquals($responseData['payload']['sensorType']['sensorTypeID'], $sensor->getSensorTypeObject()->getSensorTypeID());
        self::assertEquals($responseData['payload']['device']['deviceName'], $sensor->getDevice()->getDeviceName());
        self::assertEquals($responseData['payload']['createdBy']['email'], $sensor->getCreatedBy()->getUserIdentifier());
        self::assertEquals($responseData['payload']['createdBy']['userID'], $sensor->getCreatedBy()->getUserID());
        self::assertEquals($responseData['payload']['createdBy']['firstName'], $sensor->getCreatedBy()->getFirstName());
        self::assertEquals($responseData['payload']['createdBy']['lastName'], $sensor->getCreatedBy()->getLastName());
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
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => 1,
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
            'sensorName' => 'TestingTestingTesti' . $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => 1,
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
        self::assertStringContainsString("Sensor name cannot be longer than 20 characters", $responseData['errors'][0]);
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
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => 1,
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
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]['referenceName']]);
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findBy(['deviceID' => $device->getDeviceID()])[0];

        $formData = [
            'sensorName' => $sensor->getSensorName(),
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => 1,
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
    public function test_cannot_add_new_sensor_with_none_existent_device_id(string $sensorType, string $sensorName): void
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
            'deviceID' => $randomID,
            'pinNumber' => 1,
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

    public function test_adding_new_sensor_with_none_existent_sensor_type(): void
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
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => 1,
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
        self::assertStringContainsString('Sensor type not found for id ' . $randomID, $responseData['errors'][0]);
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
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => 1,
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
        $sensorID = $responseData['payload']['sensorID'];

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

        self::assertEquals($responseData['payload']['sensorID'], $sensor->getSensorID());
        self::assertEquals($responseData['payload']['sensorName'], $sensor->getSensorName());
        self::assertEquals($responseData['payload']['sensorType']['sensorTypeName'], $sensor->getSensorTypeObject()->getSensorType());
        self::assertEquals($responseData['payload']['sensorType']['sensorTypeID'], $sensor->getSensorTypeObject()->getSensorTypeID());
        self::assertEquals($responseData['payload']['device']['deviceName'], $sensor->getDevice()->getDeviceName());
        self::assertEquals($responseData['payload']['device']['deviceID'], $sensor->getDevice()->getDeviceID());
        self::assertEquals($responseData['payload']['createdBy']['email'], $sensor->getCreatedBy()->getUserIdentifier());
        self::assertEquals($responseData['payload']['createdBy']['userID'], $sensor->getCreatedBy()->getUserID());
        self::assertEquals($responseData['payload']['createdBy']['firstName'], $sensor->getCreatedBy()->getFirstName());
        self::assertEquals($responseData['payload']['createdBy']['lastName'], $sensor->getCreatedBy()->getLastName());
    }

    /**
     * @dataProvider newSensorExtendedDataProvider
     */
    public function test_can_add_sensor_and_card_details_admin_group_not_apart_of(string $sensorType, string $sensorName, string $class, array $sensors): void
    {
        /** @var SensorType $sensorTypeMappingObject */
        $sensorTypeMappingObject = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);
        /** @var GroupRepository $groupNameRepository */
        $groupNameRepository = $this->entityManager->getRepository(Group::class);
        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupIDs(),
        );

        $counter = 0;
        while (true) {
            $group = $groupsNotApartOf[$counter];
            $devices = $this->entityManager->getRepository(Devices::class)->findBy(['groupID' => $group->getGroupID()]);
            $counter++;
            if (!empty($devices)) {
                break;
            }
        }
        /** @var Devices[] $devices */
        $device = $devices[0];

        $devicePinsInUse = $this->deviceRepository->findAllDevicePinsInUse($device->getDeviceID());

        while (true) {
            $randomPin = random_int(0, 10);
            if (!in_array($randomPin, $devicePinsInUse)) {
                break;
            }
        }

        $formData = [
            'sensorName' => $sensorName . '1',
            'sensorTypeID' => $sensorTypeMappingObject->getSensorTypeID(),
            'deviceID' => $device->getDeviceID(),
            'pinNumber' => $randomPin,
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
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_CREATED);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'] ?? [];

        if (empty($payload)) {
            self::fail('Payload is empty');
        }
        $sensorID = $payload['sensorID'];

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

        if ($sensorTypeObject instanceof SensorTypeInterface) {
            self::assertEquals($sensorTypeObject->getSensor()->getSensorID(), $sensor->getSensorID());
            self::assertEquals($sensorTypeObject->getSensorTypeName(), $sensor->getSensorTypeObject()->getSensorType());
        }
        if ($sensorTypeObject instanceof TemperatureReadingTypeInterface) {
            self::assertEquals($sensorTypeObject->getMaxTemperature(), $sensorTypeObject->getTemperature()->getHighReading());
            self::assertEquals($sensorTypeObject->getMinTemperature(), $sensorTypeObject->getTemperature()->getLowReading());
        }
        if ($sensorTypeObject instanceof HumidityReadingTypeInterface) {
            self::assertEquals($sensorTypeObject->getMaxHumidity(), $sensorTypeObject->getHumidObject()->getHighReading());
            self::assertEquals($sensorTypeObject->getMinHumidity(), $sensorTypeObject->getHumidObject()->getLowReading());
        }
        if ($sensorTypeObject instanceof AnalogReadingTypeInterface) {
            self::assertEquals($sensorTypeObject->getMaxAnalog(), $sensorTypeObject->getAnalogObject()->getHighReading());
            self::assertEquals($sensorTypeObject->getMinAnalog(), $sensorTypeObject->getAnalogObject()->getLowReading());
        }
        if ($sensorTypeObject instanceof LatitudeReadingTypeInterface) {
            self::assertEquals($sensorTypeObject->getMaxLatitude(), $sensorTypeObject->getLatitudeObject()->getHighReading());
            self::assertEquals($sensorTypeObject->getMinLatitude(), $sensorTypeObject->getLatitudeObject()->getLowReading());
        }
        if ($sensorTypeObject instanceof MotionSensorReadingTypeInterface) {
            self::assertNull($sensorTypeObject->getMotion()->getExpectedReading());
            self::assertFalse($sensorTypeObject->getMotion()->getRequestedReading());
            self::assertFalse($sensorTypeObject->getMotion()->getCurrentReading());
        }
        if ($sensorTypeObject instanceof RelayReadingTypeInterface) {
            self::assertNull($sensorTypeObject->getRelay()->getExpectedReading());
            self::assertFalse($sensorTypeObject->getRelay()->getRequestedReading());
            self::assertFalse($sensorTypeObject->getRelay()->getCurrentReading());
        }

        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertInstanceOf($class, $sensorTypeObject);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);

        self::assertEquals(Sensor::DEFAULT_READING_INTERVAL, $sensor->getReadingInterval());
        self::assertEquals($responseData['payload']['sensorID'], $sensor->getSensorID());
        self::assertEquals($responseData['payload']['sensorName'], $sensor->getSensorName());
        self::assertEquals($responseData['payload']['sensorType']['sensorTypeName'], $sensor->getSensorTypeObject()->getSensorType());
        self::assertEquals($responseData['payload']['sensorType']['sensorTypeID'], $sensor->getSensorTypeObject()->getSensorTypeID());
        self::assertEquals($responseData['payload']['device']['deviceName'], $sensor->getDevice()->getDeviceName());
        self::assertEquals($responseData['payload']['device']['deviceID'], $sensor->getDevice()->getDeviceID());
        self::assertEquals($responseData['payload']['createdBy']['email'], $sensor->getCreatedBy()->getUserIdentifier());
        self::assertEquals($responseData['payload']['createdBy']['userID'], $sensor->getCreatedBy()->getUserID());
        self::assertEquals($responseData['payload']['createdBy']['firstName'], $sensor->getCreatedBy()->getFirstName());
        self::assertEquals($responseData['payload']['createdBy']['lastName'], $sensor->getCreatedBy()->getLastName());
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

        $pinNumber = 1;

        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => $pinNumber,
            'readingInterval' => Sensor::DEFAULT_READING_INTERVAL,
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
        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $sensorID = $responseData['payload']['sensorID'];

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

        if ($sensorTypeObject instanceof SensorTypeInterface) {
            self::assertEquals($sensorTypeObject->getSensor()->getSensorID(), $sensor->getSensorID());
            self::assertEquals($sensorTypeObject->getSensorTypeName(), $sensor->getSensorTypeObject()->getSensorType());
        }
        if ($sensorTypeObject instanceof TemperatureReadingTypeInterface) {
            self::assertEquals($sensorTypeObject->getMaxTemperature(), $sensorTypeObject->getTemperature()->getHighReading());
            self::assertEquals($sensorTypeObject->getMinTemperature(), $sensorTypeObject->getTemperature()->getLowReading());
        }
        if ($sensorTypeObject instanceof HumidityReadingTypeInterface) {
            self::assertEquals($sensorTypeObject->getMaxHumidity(), $sensorTypeObject->getHumidObject()->getHighReading());
            self::assertEquals($sensorTypeObject->getMinHumidity(), $sensorTypeObject->getHumidObject()->getLowReading());
        }
        if ($sensorTypeObject instanceof AnalogReadingTypeInterface) {
            self::assertEquals($sensorTypeObject->getMaxAnalog(), $sensorTypeObject->getAnalogObject()->getHighReading());
            self::assertEquals($sensorTypeObject->getMinAnalog(), $sensorTypeObject->getAnalogObject()->getLowReading());
        }
        if ($sensorTypeObject instanceof LatitudeReadingTypeInterface) {
            self::assertEquals($sensorTypeObject->getMaxLatitude(), $sensorTypeObject->getLatitudeObject()->getHighReading());
            self::assertEquals($sensorTypeObject->getMinLatitude(), $sensorTypeObject->getLatitudeObject()->getLowReading());
        }
        if ($sensorTypeObject instanceof MotionSensorReadingTypeInterface) {
            self::assertNull($sensorTypeObject->getMotion()->getExpectedReading());
            self::assertFalse($sensorTypeObject->getMotion()->getRequestedReading());
            self::assertFalse($sensorTypeObject->getMotion()->getCurrentReading());
        }
        if ($sensorTypeObject instanceof RelayReadingTypeInterface) {
            self::assertNull($sensorTypeObject->getRelay()->getExpectedReading());
            self::assertFalse($sensorTypeObject->getRelay()->getRequestedReading());
            self::assertFalse($sensorTypeObject->getRelay()->getCurrentReading());
        }

        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertInstanceOf($class, $sensorTypeObject);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertEquals(Sensor::DEFAULT_READING_INTERVAL, $sensor->getReadingInterval());
        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);
        self::assertEquals($responseData['payload']['sensorID'], $sensor->getSensorID());
        self::assertEquals($sensorName, $responseData['payload']['sensorName']);
        self::assertEquals($responseData['payload']['sensorName'], $sensor->getSensorName());
        self::assertEquals($responseData['payload']['pinNumber'], $sensor->getPinNumber());
        self::assertEquals($pinNumber, $sensor->getPinNumber());
        self::assertEquals(Sensor::DEFAULT_READING_INTERVAL, $responseData['payload']['readingInterval']);
        self::assertEquals($responseData['payload']['sensorType']['sensorTypeName'], $sensor->getSensorTypeObject()->getSensorType());
        self::assertEquals($responseData['payload']['sensorType']['sensorTypeID'], $sensor->getSensorTypeObject()->getSensorTypeID());
        self::assertEquals($responseData['payload']['device']['deviceName'], $sensor->getDevice()->getDeviceName());
        self::assertEquals($responseData['payload']['device']['deviceID'], $sensor->getDevice()->getDeviceID());
        self::assertEquals($responseData['payload']['createdBy']['email'], $sensor->getCreatedBy()->getUserIdentifier());
        self::assertEquals($responseData['payload']['createdBy']['userID'], $sensor->getCreatedBy()->getUserID());
        self::assertEquals($responseData['payload']['createdBy']['firstName'], $sensor->getCreatedBy()->getFirstName());
        self::assertEquals($responseData['payload']['createdBy']['lastName'], $sensor->getCreatedBy()->getLastName());
    }

    //disabled to allow for bus sensors, needs adjusting so that only bus sensors can be on same pin
//    public function test_adding_sensor_to_occupied_pin(): void
//    {
//        /** @var SensorType $sensorType */
//        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => Dht::NAME]);
//
//        $deviceToAddSensorToo = $this->deviceRepository->findOneBy(['deviceName' => ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]);
//
//        $devicePinsInUse = $this->deviceRepository->findAllDevicePinsInUse($deviceToAddSensorToo->getDeviceID());
//
//        $formData = [
//            'sensorName' => 'testing',
//            'sensorTypeID' => $sensorType->getSensorTypeID(),
//            'deviceID' => $deviceToAddSensorToo->getDeviceID(),
//            'pinNumber' => $devicePinsInUse[0],
//        ];
//
//        $jsonData = json_encode($formData);
//
//        $this->client->request(
//            Request::METHOD_POST,
//            self::ADD_NEW_SENSOR_URL,
//            $formData,
//            [],
//            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
//            $jsonData
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        self::assertStringContainsString(sprintf('Sensor with pin %d already exists', $devicePinsInUse[0]), $responseData['errors'][0]);
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }

    public function test_adding_sensor_with_negative_pin(): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => Dht::NAME]);

        $deviceToAddSensorToo = $this->deviceRepository->findOneBy(['deviceName' => ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]);

        $pinNumber = -1;
        $formData = [
            'sensorName' => 'testing',
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $deviceToAddSensorToo->getDeviceID(),
            'pinNumber' => $pinNumber,
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

        self::assertStringContainsString('pinNumber must be greater than ' . $pinNumber, $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_reading_interval_wrong_data_type(): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => Dht::NAME]);

        $deviceToAddSensorToo = $this->deviceRepository->findOneBy(['deviceName' => ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]);

        $pinNumber = 10;
        $formData = [
            'sensorName' => 'testing',
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $deviceToAddSensorToo->getDeviceID(),
            'pinNumber' => $pinNumber,
            'readingInterval' => 'string',
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

        self::assertStringContainsString('readingInterval must be a number', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_reading_interval_with_interval_too_low(): void
    {
        /** @var SensorType $sensorType */
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => Dht::NAME]);

        $deviceToAddSensorToo = $this->deviceRepository->findOneBy(['deviceName' => ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]);

        $pinNumber = 10;
        $formData = [
            'sensorName' => 'testing',
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $deviceToAddSensorToo->getDeviceID(),
            'pinNumber' => $pinNumber,
            'readingInterval' => Sensor::MIN_READING_INTERVAL - 5,
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
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString('readingInterval must be greater than ' . Sensor::MIN_READING_INTERVAL, $responseData['errors'][0]);
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

        /** @var GroupRepository $groupRepository */
        $groupRepository = $this->entityManager->getRepository(Group::class);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        /** @var Group[] $groupNames */
        $groupNames = $groupRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupIDs(),
        );

        if (empty($groupNames)) {
            self::fail('No groups found for user to add sensor to');
        }
        $groupName = $groupNames[0];

        /** @var Devices[] $devices */
        $devices = $this->entityManager->getRepository(Devices::class)->findBy(['groupID' => $groupName->getGroupID()]);

        $device = $devices[0];
        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $device->getDeviceID(),
            'pinNumber' => random_int(0, 10),
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

        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $sensorName]);
        self::assertNull($sensor);

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertStringContainsString('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertStringContainsString(APIErrorMessages::ACCESS_DENIED, $responseData['errors'][0]);
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_FORBIDDEN);
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
