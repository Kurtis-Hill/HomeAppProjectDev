<?php


namespace App\Tests\Services\ESPDeviceSensor;


use App\API\HTTPStatusCodes;
use App\Entity\Core\User;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Repository\Core\DevicesRepository;
use App\Repository\Core\UserRepository;
use App\Services\ESPDeviceSensor\Devices\DeviceServiceUser;
use App\Services\ESPDeviceSensor\SensorData\SensorUserDataService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SensorUserDataServiceTest extends WebTestCase
{
    private const ADD_NEW_SENSOR_PATH = '/HomeApp/api/sensors/add-new-sensor';

    private const API_LOGIN_USER = '/HomeApp/api/login_check';

    private const TEST_DEVICE = 101;

    private $token = null;

    private function getAPIUserData()
    {
        $client = static::createClient();
        $user = static::$container->get(UserRepository::class)->findAdminUserForTests();

        $client->loginUser($user);

        $client->request(
            'POST',
            self::API_LOGIN_USER,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"admin","password":"HomeApp1234"}'
        );

        $this->tearDown();

        $requestResponse = $client->getResponse();
        $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->userApiToken = $requestData['token'];

        return $client->getResponse();
    }

    public function test_user_login()
    {
//        dd($this->getAPIUserData()->getStatusCode());
        $this->assertEquals(HTTPStatusCodes::HTTP_OK, $this->getAPIUserData()->getStatusCode());
    }

    public function test_creating_new_valid_sensor()
    {
        $requestResponse = $this->getAPIUserData();
        $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

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
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$requestData['token']],
            'sensor-name=apitesting&device-id='.self::TEST_DEVICE.'&sensor-type='.Dht::SENSOR_TYPE_ID.''
        );

        $this->assertEquals(HTTPStatusCodes::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function test_creating_new_invalid_sensor()
    {
        $requestResponse = $this->getAPIUserData();
        $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

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
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$requestData['token']],
            'sensor-name=apitesting&device-id='.self::TEST_DEVICE.'&sensor-type='.Dht::SENSOR_TYPE_ID.''
        );

        $this->assertNotEquals(200, $client->getResponse()->getStatusCode());
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


//    public function test()
//    {
//        $client = static::createClient();
//        $user = static::$container->get(UserRepository::class)->findAdminUserForTests();
//
//        $client->loginUser($user);
//
//        $client->request(
//            'POST',
//            '/HomeApp/api/login_check',
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json'],
//            '{"username":"admin","password":"HomeApp1234"}'
//        );
//
//        $requestResponse = $client->getResponse();
//        $logingRequestStatusCode = $requestResponse->getStatusCode();
//        $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        $this->assertEquals(200, $logingRequestStatusCode);
//
//        $formData = [
//          'sensor-name' => 'apitesting',
//          'device-id' => self::TEST_DEVICE,
//          'sensor-type' => Dht::SENSOR_TYPE_ID
//        ];
//
//        $client->request(
//            'POST',
//            '/HomeApp/api/sensors/add-new-sensor',
//            $formData,
//            [],
//            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$requestData['token']],
//            'sensor-name=apitesting&device-id='.self::TEST_DEVICE.'&sensor-type='.Dht::SENSOR_TYPE_ID.''
//        );
//
//        dd($client->getResponse()->getStatusCode(), 'new one!!', $requestData['token'], $client->getResponse()->getContent());

//        $sensorId = $requestResponse->get
//        $client = static::createClient();
//        $user = static::$container->get(UserRepository::class)->findAdminUserForTests();
//
//        $client->loginUser($user);
//
//        $container = $client->getContainer();
//
//        $sensorUserService = $container->get(SensorUserDataService::class)->handleNewDeviceSubmission();

//        $deviceServiceUser = $container->get(DeviceServiceUser::class);


//        $device = static::$container->get(DevicesRepository::class)->findAdminUserForTests();

//        dd($sensorUserService);
//        dd('hey', $this->user);
//        $client = static::createClient();
//        $userRepository = static::$container->get(UserRepository::class);
//
//        $user = $userRepository->findUserById(1);
//
//        $client->loginUser($user);
//
//        dd($user);
//        $container = self::$kernel->getContainer();

//        $container = self::$container;

       // $userSensorService = self::$container->get(SensorUserDataService::class);
//        dd('heyy0', $userSensorService);
//    }
}
