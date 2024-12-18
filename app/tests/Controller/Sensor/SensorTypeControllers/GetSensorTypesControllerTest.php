<?php

namespace App\Tests\Controller\Sensor\SensorTypeControllers;

use App\Entity\Sensor\AbstractSensorType;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetSensorTypesControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_SENSOR_TYPES_URL = '/HomeApp/api/user/sensor-types';

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
            $this->userToken = $this->setUserToken($this->client);
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

    public function test_wrong_token_returns_error(): void
    {
        /** @var AbstractSensorType[] $sensorTypes */
        $sensorTypes = $this->entityManager->getRepository(AbstractSensorType::class)->findAll();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_TYPES_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken . '1', 'CONTENT_TYPE' => 'application/json']
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals('Invalid JWT Token', $responseData['message']);
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
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

        self::assertCount(count(AbstractSensorType::ALL_SENSOR_TYPES), $payload);
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_all_data_base_entries_are_returned(): void
    {
        /** @var AbstractSensorType[] $sensorTypes */
        $sensorTypes = $this->entityManager->getRepository(AbstractSensorType::class)->findAll();

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

        /** @var AbstractSensorType $sensorTypeFromDB */
        foreach ($payload as $sensorType) {
            foreach ($sensorTypes as $sensorTypeFromDB) {
                if ($sensorType['sensorTypeID'] === $sensorTypeFromDB->getSensorTypeID()) {
                    self::assertEquals($sensorType['sensorTypeName'], $sensorTypeFromDB::getReadingTypeName());
                    self::assertEquals($sensorType['sensorTypeDescription'], $sensorTypeFromDB->getDescription());
                }
            }
        }
        self::assertCount(count($sensorTypes), $payload);
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

//    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_using_wrong_http_method(string $httpVerb): void
//    {
//        $this->client->request(
//            $httpVerb,
//            self::GET_SENSOR_TYPES_URL,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
//        );
//
//        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
//    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_POST],
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }
}
