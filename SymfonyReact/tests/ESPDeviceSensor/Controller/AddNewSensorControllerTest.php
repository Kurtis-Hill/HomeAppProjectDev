<?php

namespace App\Tests\ESPDeviceSensor\Controller;

use App\API\HTTPStatusCodes;
use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Devices\Entity\Devices;
use App\Entity\Card\CardView;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\ESPDeviceSensor\Exceptions\DuplicateSensorException;
use App\Form\FormMessages;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddNewSensorControllerTest extends WebTestCase
{
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
        } catch (JsonException $e) {
            error_log($e);
        }
    }

    private function setUserToken(): void
    {
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

        $this->device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name']]);

    }

    /**
     * @return Generator
     */
    public function newSensorSimpleDataProvider(): Generator
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

    public function newSensorExtendedDataProvider(): Generator
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
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceNameID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $sensorID = $responseData['payload']['sensorNameID'];

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorNameID' => $sensorID]);

        self::assertInstanceOf(Sensors::class, $sensor);
        self::assertStringContainsString('Request Accepted Successfully Created', $responseData['title']);
        self::assertArrayHasKey('sensorNameID', $responseData['payload']);
        self::assertIsInt($responseData['payload']['sensorNameID']);
        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     * @param string $sensorType
     * @param string $sensorName
     */
    public function test_can_not_add_new_sensor_with_special_characters(string $sensorType, string $sensorName): void
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensorName' => '&' . $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceNameID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorName' => $formData['sensorName']]);

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
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensorName' => 'TestingTestingTesting' . $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceNameID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorName' => $formData['sensorName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($sensor);
        self::assertStringContainsString('Sensor name too long', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     * @param string $sensorType
     * @param string $sensorName
     */
    public function test_can_add_new_sensor_with_identical_name(string $sensorType, string $sensorName): void
    {
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName']]);
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
        $sensor = $this->entityManager->getRepository(Sensors::class)->findBy(['deviceNameID' => $device->getDeviceNameID()])[0];

        $formData = [
            'sensorName' => $sensor->getSensorName(),
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceNameID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
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
            ), $responseData['errors'][0]);
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
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $randomID,
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorName' => $formData['sensorName']]);

        self::assertNull($sensor);
        self::assertStringContainsString('Cannot find device to add sensor too', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_add_new_sensor_with_bad_sensor_type(): void
    {
        $randomID = random_int(0, 1000000);

        //ensure the random number isnt actually a valid ID
        while (1) {
            $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $randomID]);
            if (!$sensorType instanceof SensorType) {
                break;
            }
        }

        $formData = [
            'sensorName' => 'testing',
            'sensorTypeID' => $randomID,
            'deviceNameID' => $this->device->getDeviceNameID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorName' => $formData['sensorName']]);

        self::assertNull($sensor);
        self::assertStringContainsString('This value is not valid', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorExtendedDataProvider
     * @param string $sensorType
     * @param string $sensorName
     * @param string $class
     * @param array $sensors
     */
    public function test_can_add_sensor_and_card_details(string $sensorType, string $sensorName, string $class, array $sensors): void
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceNameID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
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
     * @throws JsonException
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
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceNameID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_SENSOR_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $token],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertStringContainsString('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertStringContainsString(FormMessages::ACCESS_DENIED, $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }
}
