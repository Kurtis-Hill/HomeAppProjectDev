<?php

namespace App\Tests\Sensors\SensorControllers;

use App\API\APIErrorMessages;
use App\API\HTTPStatusCodes;
use App\AppConfig\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\AppConfig\DataFixtures\ESP8266\SensorFixtures;
use App\Authentication\Controller\SecurityController;
use App\Sensors\Controller\SensorControllers\ESPSensorCurrentReadingUpdateController;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
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

class ESPSensorCurrentReadingUpdateControllerTest extends WebTestCase
{
    private const ESP_SENSOR_UPDATE = '/HomeApp/api/device/esp/update/current-reading';

    private KernelBrowser $client;

    private ?EntityManagerInterface $entityManager;

    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->setUserToken();
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
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
    public function test_sending_successful_sensor_update_requests(
        array $sensorData,
        array $message,
    ): void {
        $sendData['sensorData'] = $sensorData;
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
        self::assertEquals($message, $responseData['payload']);
        self::assertEquals('All sensor readings handled successfully', $responseData['title']);
    }

    public function successfulUpdateRequestDataProvider(): Generator
    {
        yield [
            'sensorData' => [
                [
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'sensorType' => SensorType::DHT_SENSOR,
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50
                    ]
                ]
            ],
            'message' => [
                    sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Temperature::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
                    sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
            ]
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::DALLAS_TEMPERATURE,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => 15.5,
                    ]
                ]
            ],
            'message' => [
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Temperature::READING_TYPE, SensorFixtures::SENSORS[Dallas::NAME]),
            ]
        ];

        yield [
            'sensorData' => [
                [
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'sensorType' => SensorType::BMP_SENSOR,
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50,
                        'latitude' => 50.556,
                    ]
                ],
            ],
            'message' => [
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Temperature::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Humidity::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Latitude::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
            ]
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::SOIL_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => Soil::HIGH_SOIL_READING_BOUNDARY - 10
                    ]
                ]
            ],
            'message' => [
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Analog::READING_TYPE, SensorFixtures::SENSORS[Soil::NAME]),
            ]
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::SOIL_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => Soil::HIGH_SOIL_READING_BOUNDARY - 10,
                    ],
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'sensorType' => SensorType::BMP_SENSOR,
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50,
                        'latitude' => 50.556,
                    ]
                ],
                [
                    'sensorType' => SensorType::DALLAS_TEMPERATURE,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => 15.5,
                    ]
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'sensorType' => SensorType::DHT_SENSOR,
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50
                    ]
                ],
            ],
            'message' => [
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Analog::READING_TYPE, SensorFixtures::SENSORS[Soil::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Temperature::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Humidity::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Latitude::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Temperature::READING_TYPE, SensorFixtures::SENSORS[Dallas::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Temperature::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
            ]
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::SOIL_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => Soil::HIGH_SOIL_READING_BOUNDARY - 10,
                    ],
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'sensorType' => SensorType::BMP_SENSOR,
                    'currentReadings' => [
                        'temperature' => 15.5,
                    ]
                ],
                [
                    'sensorType' => SensorType::DALLAS_TEMPERATURE,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => 15.5,
                    ]
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'sensorType' => SensorType::DHT_SENSOR,
                    'currentReadings' => [
                        'humidity' => 50
                    ]
                ],
            ],
            'message' => [
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Analog::READING_TYPE, SensorFixtures::SENSORS[Soil::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Temperature::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Temperature::READING_TYPE, SensorFixtures::SENSORS[Dallas::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
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
        array $payload,
        int $responseCode,
    ): void {
        $sendData['sensorData'] = $sensorData;
        $jsonData = json_encode($sendData, JSON_THROW_ON_ERROR);

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

//        dd($responseData, $sendData);
        self::assertEquals($responseCode, $requestResponse->getStatusCode());
        self::assertEquals($title, $responseData['title']);
        self::assertEquals($errors, $responseData['errors']);
        if (!empty($payload)) {
            self::assertEquals($payload, $responseData['payload']);
        }
    }

    public function malformedSensorUpdateDataProvider(): Generator
    {
        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::DHT_SENSOR . '1',
                    'sensorName' => SensorFixtures::SENSORS['Dht'],
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50
                    ]
                ],
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => ['Sensor type ' . SensorType::DHT_SENSOR . '1' . ' not recognised'],
            'payload' => [],
            'responseCode' => Response::HTTP_BAD_REQUEST
        ];

        yield [
            'sensorData' => [],
            'title' => 'Bad Request No Data Returned',
            'errors' => ['sensorData must contain at least 1 elements'],
            'payload' => [],
            'responseCode' => Response::HTTP_BAD_REQUEST
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::BMP_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS['Bmp'],
                ],
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => ['currentReadings cannot be empty'],
            'payload' => [],
            'responseCode' => HTTPStatusCodes::HTTP_BAD_REQUEST
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::DHT_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50
                    ],
                ],
                [
                    'sensorType' => SensorType::DHT_SENSOR,
                    'sensorName' => [SensorFixtures::SENSORS[Dht::NAME]],
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50
                    ],
                ]
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => ['sensorName must be a string you have provided array'],
            'payload' => [
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Temperature::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME])
                ],
            'responseCode' => Response::HTTP_MULTI_STATUS
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::DHT_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperature' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 15,
                        'humidity' => 50
                    ],
                ],
                [
                    'sensorType' => SensorType::DHT_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME] .'2',
                    'currentReadings' => [
                        'temperature' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 15,
                        'humidity' => 50
                    ],
                ]
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => ['Temperature settings for ' . Dht::NAME . ' sensor cannot exceed ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY . '°C you entered ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 15 . '°C'],
            'payload' => [
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Temperature::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
                sprintf(ESPSensorCurrentReadingUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE,Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME] . '2'),
            ],
            'responseCode' => Response::HTTP_MULTI_STATUS
        ];
    }

    /**
     * @dataProvider sendingOutOfRangeDataProvider
     */
    public function test_sending_out_of_range_sensor_data(
        array $sensorData,
        array $errors,
    ): void {
        $sendData['sensorData'] = $sensorData;
        $jsonData = json_encode($sendData, JSON_THROW_ON_ERROR);

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

        self::assertEquals(Response::HTTP_BAD_REQUEST, $requestResponse->getStatusCode());
        self::assertEquals(APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT, $responseData['title']);
        self::assertEquals($errors, $responseData['errors']);
    }

    public function sendingOutOfRangeDataProvider(): Generator
    {
        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::DHT_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperature' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 1,
                        'humidity' => Humidity::HIGH_READING + 1
                    ],
                ],
                [
                    'sensorType' => SensorType::DHT_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperature' => Dht::LOW_TEMPERATURE_READING_BOUNDARY - 1,
                        'humidity' => Humidity::LOW_READING - 1
                    ],
                ]
            ],
            'errors' => [
                'Temperature settings for ' . Dht::NAME . ' sensor cannot exceed '. Dht::HIGH_TEMPERATURE_READING_BOUNDARY .'°C you entered '.Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 1 .'°C',
                'Humidity for this sensor cannot be over '. Humidity::HIGH_READING .' you entered ' . Humidity::HIGH_READING + 1 . '%',
                'Temperature settings for ' . Dht::NAME . ' sensor cannot be below ' . Dht::LOW_TEMPERATURE_READING_BOUNDARY . '°C you entered ' . Dht::LOW_TEMPERATURE_READING_BOUNDARY -1  . '°C',
                'Humidity for this sensor cannot be under ' . Humidity::LOW_READING . ' you entered ' . Humidity::LOW_READING - 1 . '%'
            ],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::DALLAS_TEMPERATURE,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => Dallas::HIGH_TEMPERATURE_READING_BOUNDARY + 1,
                    ],
                ],
                [
                    'sensorType' => SensorType::DALLAS_TEMPERATURE,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => Dallas::LOW_TEMPERATURE_READING_BOUNDARY - 1,
                    ],
                ]
            ],
            'errors' => [
                'Temperature settings for ' . Dallas::NAME . ' sensor cannot exceed '. Dallas::HIGH_TEMPERATURE_READING_BOUNDARY .'°C you entered '.Dallas::HIGH_TEMPERATURE_READING_BOUNDARY + 1 .'°C',
                'Temperature settings for ' . Dallas::NAME . ' sensor cannot be below ' . Dallas::LOW_TEMPERATURE_READING_BOUNDARY . '°C you entered ' . Dallas::LOW_TEMPERATURE_READING_BOUNDARY -1  . '°C',
            ],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::BMP_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'currentReadings' => [
                        'temperature' => Bmp::HIGH_TEMPERATURE_READING_BOUNDARY + 1,
                        'humidity' => Humidity::HIGH_READING + 1,
                        'latitude' => Latitude::HIGH_READING + 1,
                    ],
                ],
                [
                    'sensorType' => SensorType::BMP_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'currentReadings' => [
                        'temperature' => Bmp::LOW_TEMPERATURE_READING_BOUNDARY - 1,
                        'humidity' => Humidity::LOW_READING - 1,
                        'latitude' => Latitude::LOW_READING - 1,
                    ],
                ]
            ],
            'errors' => [
                'Temperature settings for ' . Bmp::NAME . ' sensor cannot exceed '. Bmp::HIGH_TEMPERATURE_READING_BOUNDARY .'°C you entered '. Bmp::HIGH_TEMPERATURE_READING_BOUNDARY + 1 .'°C',
                'Humidity for this sensor cannot be over '. Humidity::HIGH_READING .' you entered ' . Humidity::HIGH_READING + 1 . '%',
                'The highest possible latitude is ' . Latitude::HIGH_READING . '° you entered ' . Latitude::HIGH_READING + 1 . '°',
                'Temperature settings for ' . Bmp::NAME . ' sensor cannot be below ' . Bmp::LOW_TEMPERATURE_READING_BOUNDARY . '°C you entered ' . Bmp::LOW_TEMPERATURE_READING_BOUNDARY -1  . '°C',
                'Humidity for this sensor cannot be under ' . Humidity::LOW_READING . ' you entered ' . Humidity::LOW_READING - 1 . '%',
                'The lowest possible latitude is ' . Latitude::LOW_READING . '° you entered ' . Latitude::LOW_READING - 1 . '°',
            ],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => SensorType::SOIL_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => Soil::HIGH_SOIL_READING_BOUNDARY + 1,
                    ],
                ],
                [
                    'sensorType' => SensorType::SOIL_SENSOR,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => Soil::LOW_SOIL_READING_BOUNDARY - 1,
                    ],
                ]
            ],
            'errors' => [
                'Reading for soil sensor cannot be over ' . Soil::HIGH_SOIL_READING_BOUNDARY . ' you entered ' . Soil::HIGH_SOIL_READING_BOUNDARY + 1,
                'Reading for soil sensor cannot be under ' . Soil::LOW_SOIL_READING_BOUNDARY . ' you entered ' . Soil::LOW_SOIL_READING_BOUNDARY - 1,
            ],
        ];
    }
}
