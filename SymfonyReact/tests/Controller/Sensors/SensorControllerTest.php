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

    // addNewSensor
    public function test_can_add_new_dht_sensor_correct_details()
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => SensorType::DHT_SENSOR]);

        $formData = [
            'sensor-name' => 'Testing',
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

        $sensorID = $responseData['responseData']['sensorNameID'];

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorNameID' => $sensorID]);

        self::assertInstanceOf(Sensors::class, $sensor);
        self::assertStringContainsString('Request Accepted Successfully Updated', $responseData['title']);
        self::assertArrayHasKey('sensorNameID', $responseData['responseData']);
        self::assertIsInt($responseData['responseData']['sensorNameID']);
        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }


    public function test_can_not_add_new_sensor_with_special_characters()
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => SensorType::DHT_SENSOR]);

        $formData = [
            'sensor-name' => '&Testing',
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

        self::assertStringContainsString('The name cannot contain any special characters, please choose a different name', $responseData['responseData'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_add_new_sensor_with_long_name()
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => SensorType::DHT_SENSOR]);

        $formData = [
            'sensor-name' => 'TestingTestingTesting',
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
        dd($this->client->getResponse(), $responseData);

        self::assertStringContainsString('Device name too long', $responseData['responseData'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_add_new_sensor_with_identicle_name()
    {
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName']]);
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => SensorType::DHT_SENSOR]);
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

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_add_new_sensor_with_bad_device_id()
    {
        $randomID = random_int(0, 1000000);
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => SensorType::DHT_SENSOR]);

        while (1) {
            $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $randomID]);
            if (!$device instanceof Devices) {
                break;
            }
        }

        $formData = [
            'sensor-name' => 'testing',
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

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_add_dht_sensor_and_card_details()
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => SensorType::DHT_SENSOR]);

        $formData = [
            'sensor-name' => 'Testing',
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

        $sensorID = $responseData['responseData']['sensorNameID'];

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorNameID' => $sensorID]);
        $dhtSensor = $this->entityManager->getRepository(Dht::class)->findOneBy(['sensorNameID' => $sensorID]);
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensorNameID' => $sensorID]);

        $temp = $this->entityManager->getRepository(Temperature::class)->findOneBy(['sensorNameID' => $sensorID]);
        $humid = $this->entityManager->getRepository(Humidity::class)->findOneBy(['sensorNameID' => $sensorID]);

        self::assertInstanceOf(Sensors::class, $sensor);
        self::assertInstanceOf(Dht::class, $dhtSensor);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertInstanceOf(Temperature::class, $temp);
        self::assertInstanceOf(Humidity::class, $humid);

        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }


    public function test_can_add_bmp_sensor_and_card_details()
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => SensorType::BMP_SENSOR]);

        $formData = [
            'sensor-name' => 'Testing',
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

        $sensorID = $responseData['responseData']['sensorNameID'];

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorNameID' => $sensorID]);
        $bmpSensor = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensorID]);
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensorNameID' => $sensorID]);

        $temp = $this->entityManager->getRepository(Temperature::class)->findOneBy(['sensorNameID' => $sensorID]);
        $humid = $this->entityManager->getRepository(Humidity::class)->findOneBy(['sensorNameID' => $sensorID]);
        $latitude = $this->entityManager->getRepository(Latitude::class)->findOneBy(['sensorNameID' => $sensorID]);

        self::assertInstanceOf(Sensors::class, $sensor);
        self::assertInstanceOf(Bmp::class, $bmpSensor);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertInstanceOf(Temperature::class, $temp);
        self::assertInstanceOf(Humidity::class, $humid);
        self::assertInstanceOf(Latitude::class, $latitude);

        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_add_dallas_sensor_and_card_details()
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => SensorType::DALLAS_TEMPERATURE]);

        $formData = [
            'sensor-name' => 'Testing',
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

        $sensorID = $responseData['responseData']['sensorNameID'];

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorNameID' => $sensorID]);
        $dallasSensor = $this->entityManager->getRepository(Dallas::class)->findOneBy(['sensorNameID' => $sensorID]);
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensorNameID' => $sensorID]);

        $temp = $this->entityManager->getRepository(Temperature::class)->findOneBy(['sensorNameID' => $sensorID]);
        self::assertInstanceOf(Sensors::class, $sensor);
        self::assertInstanceOf(Dallas::class, $dallasSensor);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertInstanceOf(Temperature::class, $temp);

        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    public function test_can_add_soil_sensor_and_card_details()
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => SensorType::SOIL_SENSOR]);

        $formData = [
            'sensor-name' => 'Testing',
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

        $sensorID = $responseData['responseData']['sensorNameID'];

        $sensor = $this->entityManager->getRepository(Sensors::class)->findOneBy(['sensorNameID' => $sensorID]);
        $soilSensor = $this->entityManager->getRepository(Soil::class)->findOneBy(['sensorNameID' => $sensorID]);
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensorNameID' => $sensorID]);

        $analog = $this->entityManager->getRepository(Analog::class)->findOneBy(['sensorNameID' => $sensorID]);

        self::assertInstanceOf(Sensors::class, $sensor);
        self::assertInstanceOf(Soil::class, $soilSensor);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertInstanceOf(Analog::class, $analog);

        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
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
    public function test_can_add_sensor_route_wrong_token()
    {
        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => SensorType::SOIL_SENSOR]);

        $formData = [
            'sensor-name' => 'Testing',
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
