<?php

namespace App\Tests\ESPDeviceSensor\Controller;

use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\DataFixtures\ESP8266\SensorTypeFixtures;
use App\Devices\Entity\Devices;
use Generator;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ESPSensorUpdateControllerTest extends WebTestCase
{
    private const ESP_SENSOR_UPDATE = '/HomeApp/api/device/update/current-reading';

    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

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

    /**
     * @return void
     * @throws JsonException
     */
    private function setUserToken(): void
    {
        if ($this->userToken === null) {
            $this->client->request(
                'POST',
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['referenceName'].'","password":"'.ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminDeviceAdminRoomAdminGroup']['AdminDeviceAdminRoomAdminGroup']['password'].'"}'
            );

            $requestResponse = $this->client->getResponse();
            $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $this->userToken = $responseData['token'];
            $this->userRefreshToken = $responseData['refreshToken'];

            $this->device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name']]);
        }
    }

    public function test_sending_sensor_update_requests(): void
    {

    }

    private function updateRequestDataProvider(): Generator
    {
        //  DHT
        yield [
            'sensorName' => SensorTypeFixtures::SENSOR_TYPE_DATA_FIXTURES
        ];
    }
}
