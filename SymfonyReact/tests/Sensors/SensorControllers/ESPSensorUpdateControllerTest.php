<?php

namespace App\Tests\Sensors\SensorControllers;

use App\API\HTTPStatusCodes;
use App\AppConfig\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\AppConfig\DataFixtures\ESP8266\SensorFixtures;
use App\Authentication\Controller\SecurityController;
use App\Sensors\Controller\SensorControllers\ESPSensorCurrentReadingUpdateController;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ESPSensorUpdateControllerTest extends WebTestCase
{
    private const ESP_SENSOR_UPDATE = '/HomeApp/api/device/esp/update/current-reading';

    private KernelBrowser $client;

    private ?EntityManagerInterface $entityManager;

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

        $this->setUserToken();
    }

    private function setUserToken(): void
    {
        if ($this->userToken === null) {
            $this->client->request(
                Request::METHOD_POST,
                SecurityController::API_DEVICE_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName'].'","password":"'.ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['password'].'"}'
            );

            $requestResponse = $this->client->getResponse();
            $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $this->userToken = $responseData['token'];
        }
    }

    /**
     * @dataProvider successfulUpdateRequestDataProvider
     */
    public function test_sending_sensor_update_requests(
        string $sensorType,
        array $sensorData,
    ): void
    {
        $sendData['sensorType'] = $sensorType;
        $sendData['sensorData'] = [$sensorData];
        $jsonData = json_encode($sendData);

        $this->client->request(
            Request::METHOD_PUT,
            self::ESP_SENSOR_UPDATE,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(Response::HTTP_OK, $requestResponse->getStatusCode());
        self::assertEquals(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE, $responseData['payload'][0]);
        self::assertEquals('Request Successful', $responseData['title']);
    }

    public function successfulUpdateRequestDataProvider(): Generator
    {
        yield [
            'sensorType' => SensorType::DHT_SENSOR,
            'sensorData' => [
                'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                'currentReadings' => [
                    'temperatureReading' => 15.5,
                    'humidityReading' => 50
                ]
            ]
        ];
        yield [
            'sensorType' => SensorType::DALLAS_TEMPERATURE,
            'sensorData' => [
                'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                'currentReadings' => [
                    'temperatureReading' => 15.5,
                ]
            ]
        ];
        yield [
            'sensorType' => SensorType::BMP_SENSOR,
            'sensorData' => [
                'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                'currentReadings' => [
                    'temperatureReading' => 15.5,
                    'humidityReading' => 50,
                    'latitudeReading' => 50.556,
                ]
            ]
        ];
        yield [
            'sensorType' => SensorType::SOIL_SENSOR,
            'sensorData' => [
                'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                'currentReadings' => [
                    'soilReading' => 155,
                ]
            ]
        ];
    }

    /**
     * @dataProvider malformedSensorUpdateDataProvider
     */
    public function test_sending_malformed_sensor_update_request(
        string $sensorType,
        array $sensorData,
        string $title,
        string $message,
        int $responseCode,
    ): void {
        if (!empty($sensorData)) {
            $dataToSend = [
                'sensorType' => $sensorType,
                'sensorData' => $sensorData
            ];
        } else {
            $dataToSend = [
                'sensorType' => $sensorType,
            ];
        }

        $jsonData = json_encode($dataToSend, JSON_THROW_ON_ERROR);
//dd($jsonData);
        $this->client->request(
            Request::METHOD_PUT,
            self::ESP_SENSOR_UPDATE,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $requestResponse = $this->client->getResponse();
//dd($requestResponse);
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals($responseCode, $requestResponse->getStatusCode());
        self::assertEquals($title, $responseData['title']);
        self::assertEquals($message, $responseData['errors'][0]);
    }

    public function malformedSensorUpdateDataProvider(): Generator
    {
        yield [
            'sensorType' => SensorType::DHT_SENSOR . '1',
            [
                'sensorData' => [
                    'sensorName' => SensorFixtures::SENSORS['Dht'],
                    'currentReadings' => [
                        'temperatureReading' => 15.5,
                        'humidityReading' => 50
                    ]
                ]
            ],
            'title' => 'Bad Request No Data Returned',
            'message' => 'Sensor type not recognised',
            'responseCode' => Response::HTTP_BAD_REQUEST
        ];

        yield [
            'sensorType' => SensorType::DALLAS_TEMPERATURE,
            'sensorData' => [],
            'title' => 'Bad Request No Data Returned',
            'message' => 'sensorData cannot be empty',
            'responseCode' => Response::HTTP_BAD_REQUEST
        ];

        yield [
            'sensorType' => SensorType::BMP_SENSOR,
            [
                'sensorData' => [
                    'sensorName' => SensorFixtures::SENSORS['Bmp'],
                ],
            ],
            'title' => 'Bad Request No Data Returned',
            'message' => 'None of the update requests could be processed',
            'responseCode' => HTTPStatusCodes::HTTP_BAD_REQUEST
        ];

        yield [
            'sensorType' => SensorType::DHT_SENSOR,
            'sensorData' => [
                [
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperatureReading' => 15.5,
                        'humidityReading' => 50
                    ],
                ],
                'sensorName' => [SensorFixtures::SENSORS[Dht::NAME]],
                'currentReadings' => [
                    'temperatureReading' => 15.5,
                    'humidityReading' => 50
                ],
            ],
            'title' => 'Part of the request was accepted',
            'message' => 'Only part of the content could be processed',
            'responseCode' => Response::HTTP_MULTI_STATUS
        ];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
