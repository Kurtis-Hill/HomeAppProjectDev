<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\SensorFixtures;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SwitchSensorControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const SWITCH_CONTROLLER = '/HomeApp/api/device/switch-sensor';

    private KernelBrowser $client;

    private ?EntityManagerInterface $entityManager;

    private SensorRepositoryInterface $sensorRepository;

    private ?string $adminToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);

        $this->adminToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::ADMIN_USER_EMAIL_ONE,
            UserDataFixtures::ADMIN_PASSWORD,
        );
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider successfulSwitchDataProvider
     */
    public function test_sending_successful_switch_sensor_request(
        string $sensorType,
        string $sensorName,
        array $currentReadings,
    ): void {
        /** @var Sensor $sensor */
        $sensor = $this->sensorRepository->findOneBy(['sensorName' => $sensorName]);

        $requestData = [
            'sensorData' => [
                [

                    'sensorName' => $sensorName,
                    'currentReadings' => $currentReadings
                ],
            ],
        ];
        $this->client->request(
            Request::METHOD_POST,
            self::SWITCH_CONTROLLER,
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($requestData)
        );
        self::assertResponseIsSuccessful();

        $response = $this->client->getResponse();

        $responseData = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $title = $responseData['title'];
        $payload = $responseData['payload'];

        self::assertEquals('All sensor readings handled successfully', $title);
        self::assertEquals(["relay data accepted for sensor $sensorName"], $payload);

        $sensorReadingTypeRepository = $this->entityManager
            ->getRepository($sensorType);


        /** @var RelayReadingTypeInterface $sensorReadingType */
        $sensorReadingType = $sensorReadingTypeRepository
            ->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertEquals($currentReadings['relay'], $sensorReadingType->getRelay()->getRequestedReading());
    }

    public function successfulSwitchDataProvider(): Generator
    {
        yield [
            'sensorType' => GenericRelay::class,
            'sensorName' => SensorFixtures::RELAY_SENSOR_NAME,
            'currentReadings' => [
                Relay::READING_TYPE => false
            ]
        ];
    }


    /**
     * @dataProvider malformedSensorUpdateDataProvider
    */
    public function test_sending_malformed_sensor_update_request(
        array $sensorData,
        string $title,
        array $errors,
    ): void {
        $sendData['sensorData'] = $sensorData;
        $jsonData = json_encode($sendData, JSON_THROW_ON_ERROR);

        $this->client->request(
            Request::METHOD_POST,
            self::SWITCH_CONTROLLER,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals($title, $responseData['title']);
        self::assertEquals($errors, $responseData['errors']);
    }

    public function malformedSensorUpdateDataProvider(): Generator
    {
        yield [
            'sensorData' => [
                [
                    'sensorType' => Bmp::NAME,
                    'sensorName' => GenericRelay::NAME,
                    'currentReadings' => [
                        Relay::READING_TYPE => false,
                    ]
                ],
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => [sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Sensor')],
        ];

        yield [
            'sensorData' => [],
            'title' => 'Bad Request No Data Returned',
            'errors' => ['sensorData must contain at least 1 elements'],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => GenericRelay::NAME,
                    'sensorName' => SensorFixtures::SENSORS[GenericRelay::NAME],
                ],
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => ['currentReadings cannot be empty'],
        ];
    }

    /**
     * @dataProvider sendingWrongDataTypesInCurrentReadingRequestDataProvider
     */
    public function test_sending_wrong_data_types_in_current_reading_request(
        array $sensorData,
        string $title,
        array $errors,
    ): void {
        $sendData['sensorData'] = $sensorData;
        $jsonData = json_encode($sendData, JSON_THROW_ON_ERROR);

        $this->client->request(
            Request::METHOD_POST,
            self::SWITCH_CONTROLLER,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals($title, $responseData['title']);
        self::assertEquals($errors, $responseData['errors']);
    }

    public function sendingWrongDataTypesInCurrentReadingRequestDataProvider(): Generator
    {
        yield [
            'sensorData' => [
                [
                    'sensorType' => GenericRelay::NAME,
                    'sensorName' => SensorFixtures::RELAY_SENSOR_NAME,
                    'currentReadings' => [
                        Relay::READING_TYPE => 'string bing',
                    ],
                ],
                [
                    'sensorType' => GenericRelay::NAME,
                    'sensorName' => SensorFixtures::RELAY_SENSOR_NAME,
                    'currentReadings' => [
                        Relay::READING_TYPE => [],
                    ],
                ]
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => [
                'Bool readings can only be true or false',
                'Bool readings can only be true or false',
            ],
            'payload' => [],
            'responseCode' => Response::HTTP_BAD_REQUEST
        ];
    }

    /**
     * @dataProvider sendingRequestWithWrongReadingTypesForSensorDataProvider
     */
    public function test_sending_request_with_wrong_reading_types_for_sensor(
        array $sensorData,
        string $title,
        array $errors,
    ): void {
        $sendData['sensorData'] = $sensorData;
        $jsonData = json_encode($sendData, JSON_THROW_ON_ERROR);

        $this->client->request(
            Request::METHOD_POST,
            self::SWITCH_CONTROLLER,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals($title, $responseData['title']);
        self::assertEquals($errors, $responseData['errors']);
    }

    public function sendingRequestWithWrongReadingTypesForSensorDataProvider(): Generator
    {
        yield [
            'sensorData' => [
                [
                    'sensorName' => SensorFixtures::RELAY_SENSOR_NAME,
                    'currentReadings' => [
                        'latitude' => Latitude::HIGH_READING,
                    ],
                ],
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => [
                Latitude::READING_TYPE . ' reading type not valid for sensor: ' . GenericRelay::NAME,
            ],
        ];
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::SWITCH_CONTROLLER,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_PATCH],
            [Request::METHOD_PUT],
            [Request::METHOD_DELETE],
        ];
    }
}
