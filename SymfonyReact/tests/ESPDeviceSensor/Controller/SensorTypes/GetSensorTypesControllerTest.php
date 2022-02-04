<?php

namespace App\Tests\ESPDeviceSensor\Controller\SensorTypes;

use App\Authentication\Controller\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\SensorType;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class GetSensorTypesControllerTest extends WebTestCase
{
    private const GET_SENSOR_TYPES_URL = '/HomeApp/api/user/sensor-types/all-types';

    private EntityManagerInterface $entityManager;

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

    public function test_all_sensortypes_that_are_documented_in_sensortypes_class_exists(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_TYPES_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json']
        );

        $requestResponse = $this->client->getResponse();

        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $payload = $responseData['payload'];

        self::assertCount(count(SensorType::ALL_SENSOR_TYPES), $payload);
    }

    public function test_all_data_base_entries_are_returned(): void
    {
        $sensorTypes = $this->entityManager->getRepository(SensorType::class)->findAll();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_TYPES_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json']
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $payload = $responseData['payload'];

        self::assertCount(count($sensorTypes), $payload);
    }
}
