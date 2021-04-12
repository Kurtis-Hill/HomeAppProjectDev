<?php


namespace App\Tests\ESPDeviceTests;


use App\DataFixtures\ESP8266DeviceFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class DeviceServiceDeviceTest
{
    private const API_LOGIN_DEVICE = '/HomeApp/device/login_check';

    private string $deviceToken;
    /**
     * @param KernelBrowser $client
     * @return mixed|string|KernelBrowser|null
     * @throws \JsonException
     */
    private function getDeviceToken(KernelBrowser $client)
    {
        if ($this->deviceToken === null) {
            $client->request(
                'POST',
                self::API_LOGIN_DEVICE,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name'].'","password":"'.ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['password'].'"}'
            );

            $requestResponse = $client->getResponse();
            $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $this->deviceToken = $requestData['token'];
            $this->deviceRefreshToken = $requestData['refreshToken'];
        }
    }


    //@TODO carnt do these yet
//    public function test_device_can_update_sensor_data()
//    {
//
//    }
    //
//    public function test_device_login()
//    {
//
//    }
//
//    public function test_device_login_jwt_token_authenticates()
//    {
//
//    }

}
