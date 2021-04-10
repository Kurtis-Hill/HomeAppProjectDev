<?php


namespace App\Tests\Services\ESPDeviceSensor;


use App\API\HTTPStatusCodes;
use App\Entity\Core\User;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Repository\Core\DevicesRepository;
use App\Repository\Core\UserRepository;
use App\Services\ESPDeviceSensor\Devices\DeviceServiceUser;
use App\Services\ESPDeviceSensor\SensorData\SensorUserDataService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SensorUserDataServiceTest extends WebTestCase
{
    private const ADD_NEW_SENSOR_PATH = '/HomeApp/api/sensors/add-new-sensor';

    private const API_LOGIN_USER = '/HomeApp/api/login_check';

    private const TEST_DEVICE = 101;

    /**
     * @var KernelBrowser|null
     */
    private static ?string $token = null;


    public function test_user_login()
    {
        self::$token = null;

        self::getToken(static::createClient());

        $this->assertNotNull(self::$token);
    }

    public function test_creating_new_valid_sensor()
    {
        $formData = [
            'sensor-name' => 'apitesting',
            'device-id' => self::TEST_DEVICE,
            'sensor-type' => Dht::SENSOR_TYPE_ID
        ];

        $client = static::createClient();

        $client->request(
            'POST',
            self::ADD_NEW_SENSOR_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.self::getToken($client)],
            'sensor-name=apitesting&device-id='.self::TEST_DEVICE.'&sensor-type='.Dht::SENSOR_TYPE_ID.''
        );

        $this->assertEquals(HTTPStatusCodes::HTTP_OK, $client->getResponse()->getStatusCode());
    }


    public function test_creating_new_invalid_sensor_by_duplicate_sensor_name()
    {
        $formData = [
            'sensor-name' => 'apitesting',
            'device-id' => self::TEST_DEVICE,
            'sensor-type' => Dht::SENSOR_TYPE_ID
        ];

        $client = static::createClient();

        $client->request(
            'POST',
            '/HomeApp/api/sensors/add-new-sensor',
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.self::getToken($client)],
            'sensor-name=apitesting&device-id='.self::TEST_DEVICE.'&sensor-type='.Dht::SENSOR_TYPE_ID.''
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }


    public function test_creating_new_invalid_sensor_by_wrong_sensor_type()
    {
        $formData = [
            'sensor-name' => 'apitesting',
            'device-id' => self::TEST_DEVICE,
            'sensor-type' => 1000
        ];

        $client = static::createClient();

        $client->request(
            'POST',
            '/HomeApp/api/sensors/add-new-sensor',
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.self::getToken($client)],
            'sensor-name=apitesting&device-id='.self::TEST_DEVICE.'&sensor-type='.Dht::SENSOR_TYPE_ID.''
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }


    public function create_new_dallas_sensor_by_request_test()
    {

    }
    public function create_new_soil_sensor_by_request_test()
    {

    }
    public function create_new_bmp_sensor_by_request_test()
    {

    }

    public function update_sensor_outofbounds_reading()
    {

    }

    private static function getToken(KernelBrowser $client)
    {
        if (self::$token === null) {
            $client->request(
                'POST',
                self::API_LOGIN_USER,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"admin-test-email@testing.com","password":"admin1234"}'
            );

            $requestResponse = $client->getResponse();
            $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::$token = $requestData['token'];

            return self::$token;
        }

        return self::$token;
    }
}
