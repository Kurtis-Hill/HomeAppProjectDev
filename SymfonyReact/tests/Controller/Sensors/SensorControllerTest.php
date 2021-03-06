<?php


namespace App\Tests\Controller\Sensors;


use App\API\HTTPStatusCodes;
use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Card\CardView;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Entity\Sensors\SensorTypes\Bmp;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Soil;
use App\Form\FormMessages;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SensorControllerTest extends WebTestCase
{
    private const GET_SENSOR_TYPES_URL = '/HomeApp/api/sensors/types';

    private const ADD_NEW_SENSOR_URL = '/HomeApp/api/sensors/add-new-sensor';

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    /**
     * @var ?Devices
     */
    private ?Devices $device;

    /**
     * @var string|null
     */
    private ?string $userToken = null;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name']]);
            $this->setUserToken();
        } catch (\JsonException $e) {
            error_log($e);
        }
    }

    /**
     * @return mixed|string|KernelBrowser|null
     * @throws \JsonException
     */
    private function setUserToken()
    {
        if ($this->userToken === null) {
            $this->client->request(
                'POST',
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
            );

            $requestResponse = $this->client->getResponse();
            $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $this->userToken = $responseData['token'];
            $this->userRefreshToken = $responseData['refreshToken'];

            $this->device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name']]);
        }
    }

    /**
     * @return \Generator
     */
    public function newSensorSimpleDataProvider(): \Generator
    {
        yield [
            'sensor' => SensorType::DHT_SENSOR,
            'sensorName' => 'dhtTest'
        ];

        yield [
            'sensor' => SensorType::BMP_SENSOR,
            'sensorName' => 'bmpTest'
        ];

        yield [
            'sensor' => SensorType::SOIL_SENSOR,
            'sensorName' => 'soilTest'
        ];

        yield [
            'sensor' => SensorType::DALLAS_TEMPERATURE,
            'sensorName' => 'dallasTest'
        ];
    }

    public function newSensorExtendedDataProvider(): \Generator
    {
        yield [
            'sensor' => SensorType::DHT_SENSOR,
            'sensorName' => 'dhtTest',
            'class' => Dht::class,
            [
                'temperature' => Temperature::class,
                'humidity' => Humidity::class,
            ]
        ];

        yield [
            'sensor' => SensorType::BMP_SENSOR,
            'sensorName' => 'bmpTest',
            'class' => Bmp::class,
            [
                'temperature' => Temperature::class,
                'humidity' => Humidity::class,
                'latitude' => Latitude::class
            ]
        ];

        yield [
            'sensor' => SensorType::SOIL_SENSOR,
            'sensorName' => 'soilTest',
            'class' => Soil::class,
            [
                'analog' => Analog::class
            ]
        ];

        yield [
            'sensor' => SensorType::DALLAS_TEMPERATURE,
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
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensor-name' => $sensorName,
            'sensor-type' => $sensorType->getSensorTypeID(),
            'device-id' => $this->device->getDeviceNameID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $sensorID = $responseData['payload']['sensorNameID'];

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorNameID' => $sensorID]);

        self::assertInstanceOf(Sensors::class, $sensor);
        self::assertStringContainsString('Request Accepted Successfully Updated', $responseData['title']);
        self::assertArrayHasKey('sensorNameID', $responseData['payload']);
        self::assertIsInt($responseData['payload']['sensorNameID']);
        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }


    /**
     * @dataProvider newSensorSimpleDataProvider
     * @param string $sensorType
     * @param string $sensorName
     */
    public function test_can_not_add_new_sensor_with_special_characters(string $sensorType, string $sensorName)
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensor-name' => '&' . $sensorName,
            'sensor-type' => $sensorType->getSensorTypeID(),
            'device-id' => $this->device->getDeviceNameID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorName' => $formData['sensor-name']]);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($sensor);
        self::assertStringContainsString('The name cannot contain any special characters, please choose a different name', $responseData['payload']['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     * @param string $sensorType
     * @param string $sensorName
     */
    public function test_can_not_add_new_sensor_with_long_name(string $sensorType, string $sensorName)
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensor-name' => 'TestingTestingTesting' . $sensorName,
            'sensor-type' => $sensorType->getSensorTypeID(),
            'device-id' => $this->device->getDeviceNameID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorName' => $formData['sensor-name']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($sensor);
        self::assertStringContainsString('Sensor name too long', $responseData['payload']['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     * @param string $sensorType
     * @param string $sensorName
     */
    public function test_can_add_new_sensor_with_identicle_name(string $sensorType, string $sensorName)
    {
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName']]);
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
        $sensor = $this->entityManager->getRepository(Sensors::class)->findBy(['deviceNameID' => $device->getDeviceNameID()])[0];

        $formData = [
            'sensor-name' => $sensor->getSensorName(),
            'sensor-type' => $sensorType->getSensorTypeID(),
            'device-id' => $this->device->getDeviceNameID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString('You already have a sensor named '. $sensor->getSensorName(), $responseData['payload']['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     * @param string $sensorType
     * @param string $sensorName
     */
    public function test_can_add_new_sensor_with_bad_device_id(string $sensorType, string $sensorName): void
    {
        $randomID = random_int(0, 1000000);
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        while (1) {
            $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $randomID]);
            if (!$device instanceof Devices) {
                break;
            }
        }

        $formData = [
            'sensor-name' => $sensorName,
            'sensor-type' => $sensorType->getSensorTypeID(),
            'device-id' => $randomID,
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorName' => $formData['sensor-name']]);

        self::assertNull($sensor);
        self::assertStringContainsString('Cannot find device to add sensor too', $responseData['payload']['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_add_new_sensor_with_bad_sensor_type()
    {
        $randomID = random_int(0, 1000000);

        while (1) {
            $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $randomID]);
            if (!$sensorType instanceof SensorType) {
                break;
            }
        }

        $formData = [
            'sensor-name' => 'testing',
            'sensor-type' => $randomID,
            'device-id' => $this->device->getDeviceNameID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorName' => $formData['sensor-name']]);

        self::assertNull($sensor);
        self::assertStringContainsString('This value is not valid', $responseData['payload']['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorExtendedDataProvider
     * @param string $sensorType
     * @param string $sensorName
     * @param StandardSensorTypeInterface $class
     */
    public function test_can_add_sensor_and_card_details(string $sensorType, string $sensorName, string $class, array $sensors)
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensor-name' => $sensorName,
            'sensor-type' => $sensorType->getSensorTypeID(),
            'device-id' => $this->device->getDeviceNameID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $sensorID = $responseData['payload']['sensorNameID'];

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorNameID' => $sensorID]);
        $dhtSensor = $this->entityManager->getRepository($class)->findOneBy(['sensorNameID' => $sensorID]);
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensorNameID' => $sensorID]);

        foreach ($sensors as $sensorTypeClass) {
            $sensorType = $this->entityManager->getRepository($sensorTypeClass)->findOneBy(['sensorNameID' => $sensorID]);
            self::assertInstanceOf($sensorTypeClass, $sensorType);
        }
        self::assertInstanceOf(Sensors::class, $sensor);
        self::assertInstanceOf($class, $dhtSensor);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     * @throws \JsonException
     */
    public function test_add_new_sensor_when_not_part_of_associate_group(string $sensorType, string $sensorName): void
    {
        $this->client->request(
            'POST',
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"'.UserDataFixtures::SECOND_REGULAR_USER_ISOLATED.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $token = $responseData['token'];

        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensor-name' => $sensorName,
            'sensor-type' => $sensorType->getSensorTypeID(),
            'device-id' => $this->device->getDeviceNameID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $token],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertStringContainsString('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertStringContainsString(FormMessages::ACCESS_DENIED, $responseData['payload']['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }


    // returnAllSensorTypes
    public function test_return_all_sensor_types()
    {
        $this->client->request(
            'GET',
            self::GET_SENSOR_TYPES_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $totalSensorTypes = count(SensorType::SENSOR_TYPES);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $sensorTypeCount = count($responseData);

        foreach ($responseData as $sensorType) {
            self::assertIsInt($sensorType['sensorTypeID']);
            self::assertContains($sensorType['sensorType'], SensorType::SENSOR_TYPES);
            self::assertArrayHasKey('description', $sensorType);
        }

        self::assertEquals($totalSensorTypes, $sensorTypeCount);

        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    // Access Tests

    /**
     * @dataProvider newSensorSimpleDataProvider
     * @param string $sensorType
     * @param string $sensorName
     */
    public function test_can_add_sensor_route_wrong_token(string $sensorType, string $sensorName): void
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensor-name' => $sensorName,
            'sensor-type' => $sensorType->getSensorTypeID(),
            'device-id' => $this->device->getDeviceNameID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken. '1'],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_UNAUTHORISED, $this->client->getResponse()->getStatusCode());
    }

    public function test_get_sensor_types_route_wrong_token()
    {
        $this->client->request(
            'GET',
            self::GET_SENSOR_TYPES_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken. '1'],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_UNAUTHORISED, $this->client->getResponse()->getStatusCode());
    }
}
