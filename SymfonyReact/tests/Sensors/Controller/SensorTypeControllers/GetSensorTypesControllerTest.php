<?php

namespace Sensors\Controller\SensorTypeControllers;

use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Doctrine\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Authentication\Controller\SecurityController;
use App\Devices\Entity\Devices;
use App\Sensors\Entity\SensorType;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetSensorTypesControllerTest extends WebTestCase
{
    private const GET_SENSOR_TYPES_URL = '/HomeApp/api/user/sensor-types/all';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->userToken = $this->setUserToken();
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

    private function setUserToken(bool $forceToken = false): string
    {
        if ($this->userToken === null || $forceToken === true) {
            $this->client->request(
                Request::METHOD_POST,
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"' . UserDataFixtures::ADMIN_USER . '","password":"' . UserDataFixtures::ADMIN_PASSWORD . '"}'
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
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
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
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}