<?php

namespace App\Tests\ESPDeviceSensor\Controller;

use App\API\APIErrorMessages;
use App\API\HTTPStatusCodes;
use App\Authentication\Controller\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\ESPDeviceSensor\Exceptions\DuplicateSensorException;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class AddNewSensorControllerTest extends WebTestCase
{
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
            $this->device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name']]);
            $this->userToken = $this->setUserToken();
        } catch (JsonException $e) {
            error_log($e);
        }
    }

    private function setUserToken(bool $forceToken = false): string
    {
        if ($this->userToken === null || $forceToken === true) {
            $this->client->request(
                Request::METHOD_POST,
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
            );

            $requestResponse = $this->client->getResponse();
            $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            return $responseData['token'];
        }

        return $this->userToken;
    }
    
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
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $sensorID = $responseData['payload']['sensorNameID'];

        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorNameID' => $sensorID]);

        self::assertInstanceOf(Sensor::class, $sensor);
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
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData,
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

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
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensorName' => 'TestingTestingTesting' . $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceNameID(),
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
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);

        $formData = [
            'sensorName' => 'T',
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceNameID(),
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

        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($sensor);
        self::assertStringContainsString('Sensor name must be at least 2 characters long', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     */
    public function test_can_add_new_sensor_with_identical_name(string $sensorType): void
    {
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName']]);
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => $sensorType]);
        $sensor = $this->entityManager->getRepository(Sensor::class)->findBy(['deviceNameID' => $device->getDeviceNameID()])[0];

        $formData = [
            'sensorName' => $sensor->getSensorName(),
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceNameID' => $this->device->getDeviceNameID(),
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
    public function test_can_add_new_sensor_with_bad_device_id(string $sensorType, string $sensorName): void
    {
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
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);

        self::assertNull($sensor);
        self::assertStringContainsString('Device not found', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_add_new_sensor_with_bad_sensor_type(): void
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
            'deviceNameID' => $this->device->getDeviceNameID(),
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

        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);

        self::assertNull($sensor);
        self::assertStringContainsString('SensorType not found', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorExtendedDataProvider
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
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $sensorID = $responseData['payload']['sensorNameID'];

        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorNameID' => $sensorID]);
        $sensorTypeObject = $this->entityManager->getRepository($class)->findOneBy(['sensorNameID' => $sensorID]);
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensorNameID' => $sensorID]);

        foreach ($sensors as $sensorTypeClass) {
            $sensorType = $this->entityManager->getRepository($sensorTypeClass)->findOneBy(['sensorNameID' => $sensorID]);
            self::assertInstanceOf($sensorTypeClass, $sensorType);
        }

        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertInstanceOf($class, $sensorTypeObject);
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
            Request::METHOD_POST,
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"'.UserDataFixtures::REGULAR_USER.'","password":"'.UserDataFixtures::REGULAR_PASSWORD.'"}'
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
        self::assertEquals(HTTPStatusCodes::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
