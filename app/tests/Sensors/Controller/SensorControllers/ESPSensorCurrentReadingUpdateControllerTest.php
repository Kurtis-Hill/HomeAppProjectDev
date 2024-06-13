<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\DataFixtures\ESP8266\SensorFixtures;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Services\API\APIErrorMessages;
use App\Services\API\HTTPStatusCodes;
use App\Services\Sensor\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandler;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ESPSensorCurrentReadingUpdateControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ESP_SENSOR_UPDATE = '/HomeApp/api/device/esp/update/current-reading';

    private KernelBrowser $client;

    private ?EntityManagerInterface $entityManager;

    private ?string $deviceToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->deviceToken = $this->setDeviceToken(
            $this->client,
            ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName'],
            ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['password']
        );
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
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
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->deviceToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $requestResponse = $this->client->getResponse();
//        dd($requestResponse->getContent());
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
                    'sensorType' => Dht::NAME,
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50
                    ]
                ]
            ],
            'message' => [
                    sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
                    sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
            ]
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Dallas::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => 15.5,
                    ]
                ]
            ],
            'message' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Dallas::NAME]),
            ]
        ];

        yield [
            'sensorData' => [
                [
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'sensorType' => Bmp::NAME,
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50,
                        'latitude' => 50.556,
                    ]
                ],
            ],
            'message' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Latitude::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
            ]
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Soil::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => Soil::HIGH_SOIL_READING_BOUNDARY - 10
                    ]
                ]
            ],
            'message' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Analog::READING_TYPE, SensorFixtures::SENSORS[Soil::NAME]),
            ]
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Soil::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => Soil::HIGH_SOIL_READING_BOUNDARY - 10,
                    ],
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'sensorType' => Bmp::NAME,
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50,
                        'latitude' => 50.556,
                    ]
                ],
                [
                    'sensorType' => Dallas::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => 15.5,
                    ]
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'sensorType' => Dht::NAME,
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50
                    ]
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[GenericMotion::NAME],
                    'sensorType' => GenericMotion::NAME,
                    'currentReadings' => [
                        'motion' => true,
                    ],
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[GenericRelay::NAME],
                    'sensorType' => GenericRelay::NAME,
                    'currentReadings' => [
                        'relay' => true,
                    ],
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[LDR::NAME],
                    'sensorType' => LDR::NAME,
                    'currentReadings' => [
                        'analog' => 500,
                    ],
                ]
            ],
            'message' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Analog::READING_TYPE, SensorFixtures::SENSORS[Soil::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Latitude::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Dallas::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Motion::READING_TYPE, SensorFixtures::SENSORS[GenericMotion::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Relay::READING_TYPE, SensorFixtures::SENSORS[GenericRelay::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Analog::READING_TYPE, SensorFixtures::SENSORS[LDR::NAME]),
            ]
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Soil::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => Soil::HIGH_SOIL_READING_BOUNDARY - 10,
                    ],
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'sensorType' => Bmp::NAME,
                    'currentReadings' => [
                        'temperature' => 15.5,
                    ]
                ],
                [
                    'sensorType' => Dallas::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => 15.5,
                    ]
                ],
                [
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'sensorType' => Dht::NAME,
                    'currentReadings' => [
                        'humidity' => 50
                    ]
                ],
            ],
            'message' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Analog::READING_TYPE, SensorFixtures::SENSORS[Soil::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Bmp::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Dallas::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
            ]
        ];

        // Motion
        yield [
            'sensorData' => [
                [
                    'sensorType' => GenericMotion::NAME,
                    'sensorName' => SensorFixtures::SENSORS[GenericMotion::NAME],
                    'currentReadings' => [
                        'motion' => true,
                    ],
                ],
            ],
            'message' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Motion::READING_TYPE, SensorFixtures::SENSORS[GenericMotion::NAME]),
            ]
        ];

        // Generic Relay
        yield [
            'sensorData' => [
                [
                    'sensorType' => GenericRelay::NAME,
                    'sensorName' => SensorFixtures::SENSORS[GenericRelay::NAME],
                    'currentReadings' => [
                        'relay' => true,
                    ],
                ],
                [
                    'sensorType' => GenericRelay::NAME,
                    'sensorName' => 'UsDev2Relay',
                    'currentReadings' => [
                        'relay' => true,
                    ],
                ],
            ],
            'message' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Relay::READING_TYPE, SensorFixtures::SENSORS[GenericRelay::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Relay::READING_TYPE, 'UsDev2Relay'),
            ]
        ];

        //LDR
        yield [
            'sensorData' => [
                [
                    'sensorType' => LDR::NAME,
                    'sensorName' => SensorFixtures::SENSORS[LDR::NAME],
                    'currentReadings' => [
                        'analog' => 500,
                    ],
                ],
            ],
            'message' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Analog::READING_TYPE, SensorFixtures::SENSORS[LDR::NAME]),
            ]
        ];

        // SHT
        yield [
            'sensorData' => [
                [
                    'sensorType' => Sht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Sht::NAME],
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50,
                    ],
                ],
            ],
            'message' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Sht::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Sht::NAME]),
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
            Request::METHOD_PUT,
            self::ESP_SENSOR_UPDATE,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->deviceToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $requestResponse = $this->client->getResponse();
//        dd($requestResponse->getContent());
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $requestResponse->getStatusCode());
        self::assertEquals($title, $responseData['title']);
        self::assertEquals($errors, $responseData['errors']);
    }

    public function malformedSensorUpdateDataProvider(): Generator
    {
        yield [
            'sensorData' => [
                [
                    'sensorType' => Dht::NAME . '1',
                    'sensorName' => Dht::NAME,
                    'currentReadings' => [
                        'temperature' => 15.5,
                        'humidity' => 50
                    ]
                ],
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => ['sensorType must be one of "Bmp", "Soil", "Dallas", "Dht", "GenericMotion", "GenericRelay", "Ldr", "Sht"'],
        ];

        yield [
            'sensorData' => [],
            'title' => 'Bad Request No Data Returned',
            'errors' => ['sensorData must contain at least 1 elements'],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Bmp::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                ],
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => ['currentReadings cannot be empty'],
        ];
    }

    /**
     * @dataProvider mixedGoodBadRequestDataProvider
     */
    public function test_sending_incorrect_parts_of_request(
        array $sensorData,
        string $title,
        array $errors,
        array $payload,
    ): void {
        $sendData['sensorData'] = $sensorData;
        $jsonData = json_encode($sendData, JSON_THROW_ON_ERROR);

        $this->client->request(
            Request::METHOD_PUT,
            self::ESP_SENSOR_UPDATE,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->deviceToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(Response::HTTP_MULTI_STATUS, $requestResponse->getStatusCode());
        self::assertEquals($title, $responseData['title']);
        self::assertEquals($payload, $responseData['payload']);
        self::assertEquals($errors, $responseData['errors']);
    }

    public function mixedGoodBadRequestDataProvider(): Generator
    {
        // taken out due to doc extractor array sensor name is a type exception
//        yield [
//            'sensorData' => [
//                [
//                    'sensorType' => Dht::NAME,
//                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
//                    'currentReadings' => [
//                        'temperature' => 15.5,
//                        'humidity' => 50
//                    ],
//                ],
//                [
//                    'sensorType' => Dht::NAME,
//                    'sensorName' => [SensorFixtures::SENSORS[Dht::NAME]],
//                    'currentReadings' => [
//                        'temperature' => 15.5,
//                        'humidity' => 50
//                    ],
//                ]
//            ],
//            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
//            'errors' => ['sensorName must be a string you have provided array'],
//            'payload' => [
//                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
//                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME])
//            ],
//        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Dht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperature' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 15,
                        'humidity' => 50
                    ],
                ],
                [
                    'sensorType' => Dht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME] .'2',
                    'currentReadings' => [
                        'temperature' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 15,
                        'humidity' => 50
                    ],
                ]
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => [ucfirst(Temperature::READING_TYPE) . ' settings for ' . Dht::NAME . ' sensor cannot exceed ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL .' you entered ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 15 . Temperature::READING_SYMBOL],
            'payload' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME] . '2'),
            ],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Dht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperature' => 'string bing',
                        'humidity' => []
                    ],
                ],
                [
                    'sensorType' => Dht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME] .'2',
                    'currentReadings' => [
                        'temperature' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 15,
                        'humidity' => 50
                    ],
                ]
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => [
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Dht::NAME . ' sensor cannot exceed ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 15 . Temperature::READING_SYMBOL,
                'The submitted value is not a number "array"',
                'The submitted value is not a number "string"',

            ],
            'payload' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME] . '2'),
            ],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Dht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperature' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 15,
                        'humidity' => 'string bing'
                    ],
                ],
                [
                    'sensorType' => Dht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME] .'2',
                    'currentReadings' => [
                        'temperature' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 15,
                        'humidity' => []
                    ],
                ]
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => [
                'The submitted value is not a number "array"',
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Dht::NAME . ' sensor cannot exceed ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 15 . Temperature::READING_SYMBOL,
                'The submitted value is not a number "string"',
            ],
            'payload' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Dht::NAME]),
            ],
        ];

        // SHT
        yield [
            'sensorData' => [
                [
                    'sensorType' => Sht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Sht::NAME],
                    'currentReadings' => [
                        'temperature' => 'string bing',
                        'humidity' => []
                    ],
                ],
                [
                    'sensorType' => Sht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Sht::NAME] .'2',
                    'currentReadings' => [
                        'temperature' => Sht::HIGH_TEMPERATURE_READING_BOUNDARY + 15,
                        'humidity' => 50
                    ],
                ]
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => [
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Sht::NAME . ' sensor cannot exceed ' . Sht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Sht::HIGH_TEMPERATURE_READING_BOUNDARY + 15 . Temperature::READING_SYMBOL,
                'The submitted value is not a number "array"',
                'The submitted value is not a number "string"',

            ],
            'payload' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Humidity::READING_TYPE, SensorFixtures::SENSORS[Sht::NAME] . '2'),
            ],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Sht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Sht::NAME],
                    'currentReadings' => [
                        'temperature' => Sht::HIGH_TEMPERATURE_READING_BOUNDARY - 15,
                        'humidity' => 'string bing'
                    ],
                ],
                [
                    'sensorType' => Sht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Sht::NAME] .'2',
                    'currentReadings' => [
                        'temperature' => Sht::HIGH_TEMPERATURE_READING_BOUNDARY + 15,
                        'humidity' => []
                    ],
                ]
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => [
                'The submitted value is not a number "array"',
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Sht::NAME . ' sensor cannot exceed ' . Sht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Sht::HIGH_TEMPERATURE_READING_BOUNDARY + 15 . Temperature::READING_SYMBOL,
                'The submitted value is not a number "string"',
            ],
            'payload' => [
                sprintf(CurrentReadingSensorDataRequestHandler::SENSOR_UPDATE_SUCCESS_MESSAGE, Temperature::READING_TYPE, SensorFixtures::SENSORS[Sht::NAME]),
            ],
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
            Request::METHOD_PUT,
            self::ESP_SENSOR_UPDATE,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->deviceToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );
//        dd($this->client->getResponse()->getContent());

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $requestResponse->getStatusCode());
        self::assertEquals($title, $responseData['title']);
        self::assertEquals($errors, $responseData['errors']);
    }

    public function sendingWrongDataTypesInCurrentReadingRequestDataProvider(): Generator
    {
        yield [
            'sensorData' => [
                [
                    'sensorType' => Soil::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => 'string bing',
                    ],
                ],
                [
                    'sensorType' => Soil::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME] .'2',
                    'currentReadings' => [
                        'analog' => [],
                    ],
                ]
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => [
                'The submitted value is not a number array',
                'The submitted value is not a number string',
            ],
            'payload' => [],
            'responseCode' => Response::HTTP_BAD_REQUEST
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Bmp::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'latitude' => 'string bing',
                    ],
                ],
                [
                    'sensorType' => Bmp::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME] .'2',
                    'currentReadings' => [
                        'latitude' => [],
                    ],
                ]
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => [
                'The submitted value is not a number array',
                'The submitted value is not a number string',
            ],
            'payload' => [],
            'responseCode' => Response::HTTP_BAD_REQUEST
        ];

        // Generic Motion
        yield [
            'sensorData' => [
                [
                    'sensorType' => GenericMotion::NAME,
                    'sensorName' => SensorFixtures::SENSORS[GenericMotion::NAME],
                    'currentReadings' => [
                        'motion' => 'string bing',
                    ],
                ],
                [
                    'sensorType' => GenericMotion::NAME,
                    'sensorName' => SensorFixtures::SENSORS[GenericMotion::NAME] .'2',
                    'currentReadings' => [
                        'motion' => [],
                    ],
                ]
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => [
                'Bool readings can only be true or false',
            ],
            'payload' => [],
            'responseCode' => Response::HTTP_BAD_REQUEST
        ];

        // Generic Relay
        yield [
            'sensorData' => [
                [
                    'sensorType' => GenericRelay::NAME,
                    'sensorName' => SensorFixtures::SENSORS[GenericRelay::NAME],
                    'currentReadings' => [
                        'relay' => 'string bing',
                    ],
                ],
                [
                    'sensorType' => GenericRelay::NAME,
                    'sensorName' => SensorFixtures::SENSORS[GenericRelay::NAME] .'2',
                    'currentReadings' => [
                        'relay' => [],
                    ],
                ]
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => [
                'Bool readings can only be true or false',
            ],
            'payload' => [],
            'responseCode' => Response::HTTP_BAD_REQUEST
        ];

        //LDR
        yield [
            'sensorData' => [
                [
                    'sensorType' => LDR::NAME,
                    'sensorName' => SensorFixtures::SENSORS[LDR::NAME],
                    'currentReadings' => [
                        'analog' => 'string bing',
                    ],
                ],
                [
                    'sensorType' => LDR::NAME,
                    'sensorName' => SensorFixtures::SENSORS[LDR::NAME] .'2',
                    'currentReadings' => [
                        'analog' => [],
                    ],
                ]
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => [
                'The submitted value is not a number array',
                'The submitted value is not a number string',
            ],
            'payload' => [],
            'responseCode' => Response::HTTP_BAD_REQUEST
        ];

        // SHT
        yield [
            'sensorData' => [
                [
                    'sensorType' => Sht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Sht::NAME],
                    'currentReadings' => [
                        'temperature' => 'string bing',
                    ],
                ],
                [
                    'sensorType' => Sht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Sht::NAME] .'2',
                    'currentReadings' => [
                        'temperature' => [],
                    ],
                ]
            ],
            'title' => APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT,
            'errors' => [
                'The submitted value is not a number "array"',
                'The submitted value is not a number "string"',
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
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->deviceToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals($responseCode, $requestResponse->getStatusCode());
        self::assertEquals($title, $responseData['title']);
        self::assertEquals($errors, $responseData['errors']);
        self::assertEquals($payload, $responseData['payload']);
    }

    public function sendingRequestWithWrongReadingTypesForSensorDataProvider(): Generator
    {
        yield [
            'sensorData' => [
                [
                    'sensorType' => Bmp::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'currentReadings' => [
                        'latitude' => Latitude::HIGH_READING,
                        'analog' => 1234,
                    ],
                ],
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => [
                Analog::READING_TYPE . ' reading type not valid for sensor: ' . Bmp::NAME,
            ],
            'payload' => [Latitude::READING_TYPE.' data accepted for sensor ' . SensorFixtures::SENSORS[Bmp::NAME]],
            'responseCode' => Response::HTTP_MULTI_STATUS
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Dallas::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => Dallas::HIGH_TEMPERATURE_READING_BOUNDARY - 10,
                        'latitude' => Latitude::HIGH_READING,
                    ],
                ],
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => [
                Latitude::READING_TYPE . ' reading type not valid for sensor: ' . Dallas::NAME,
            ],
            'payload' => [Temperature::READING_TYPE.' data accepted for sensor ' . SensorFixtures::SENSORS[Dallas::NAME]],
            'responseCode' => Response::HTTP_MULTI_STATUS
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Dht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperature' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 10,
                        'latitude' => Latitude::HIGH_READING,
                    ],
                ],
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => [
                Latitude::READING_TYPE . ' reading type not valid for sensor: ' . Dht::NAME,
            ],
            'payload' => [Temperature::READING_TYPE.' data accepted for sensor ' . SensorFixtures::SENSORS[Dht::NAME]],
            'responseCode' => Response::HTTP_MULTI_STATUS
        ];

        //LDR
        yield [
            'sensorData' => [
                [
                    'sensorType' => LDR::NAME,
                    'sensorName' => SensorFixtures::SENSORS[LDR::NAME],
                    'currentReadings' => [
                        'analog' => LDR::HIGH_READING - 5,
                        'latitude' => Latitude::HIGH_READING,
                    ],
                ],
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => [
                Latitude::READING_TYPE . ' reading type not valid for sensor: ' . LDR::NAME,
            ],
            'payload' => [Analog::READING_TYPE.' data accepted for sensor ' . SensorFixtures::SENSORS[LDR::NAME]],
            'responseCode' => Response::HTTP_MULTI_STATUS
        ];

        // SHT
        yield [
            'sensorData' => [
                [
                    'sensorType' => Sht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Sht::NAME],
                    'currentReadings' => [
                        'temperature' => Sht::HIGH_TEMPERATURE_READING_BOUNDARY - 10,
                        'latitude' => Latitude::HIGH_READING,
                    ],
                ],
            ],
            'title' => APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            'errors' => [
                Latitude::READING_TYPE . ' reading type not valid for sensor: ' . Sht::NAME,
            ],
            'payload' => [Temperature::READING_TYPE.' data accepted for sensor ' . SensorFixtures::SENSORS[Sht::NAME]],
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
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->deviceToken, 'CONTENT_TYPE' => 'application/json'],
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
                    'sensorType' => Dht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperature' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 1,
                        'humidity' => Humidity::HIGH_READING + 1
                    ],
                ],
                [
                    'sensorType' => Dht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
                    'currentReadings' => [
                        'temperature' => Dht::LOW_TEMPERATURE_READING_BOUNDARY - 1,
                        'humidity' => Humidity::LOW_READING - 1
                    ],
                ]
            ],
            'errors' => [
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be under ' . Humidity::LOW_READING . Humidity::READING_SYMBOL . ' you entered ' . Humidity::LOW_READING - 1 . Humidity::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Dht::NAME . ' sensor cannot be below ' . Dht::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Dht::LOW_TEMPERATURE_READING_BOUNDARY -1  . Temperature::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be over '. Humidity::HIGH_READING . Humidity::READING_SYMBOL . ' you entered ' . Humidity::HIGH_READING + 1 . Humidity::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Dht::NAME . ' sensor cannot exceed '. Dht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered '.Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 1 . Temperature::READING_SYMBOL,
            ],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Dallas::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => Dallas::HIGH_TEMPERATURE_READING_BOUNDARY + 1,
                    ],
                ],
                [
                    'sensorType' => Dallas::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Dallas::NAME],
                    'currentReadings' => [
                        'temperature' => Dallas::LOW_TEMPERATURE_READING_BOUNDARY - 1,
                    ],
                ]
            ],
            'errors' => [
                ucfirst(Temperature::READING_TYPE) .' settings for ' . Dallas::NAME . ' sensor cannot be below ' . Dallas::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Dallas::LOW_TEMPERATURE_READING_BOUNDARY -1  . Temperature::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Dallas::NAME . ' sensor cannot exceed '. Dallas::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL .' you entered '.Dallas::HIGH_TEMPERATURE_READING_BOUNDARY + 1 . Temperature::READING_SYMBOL,
            ],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Bmp::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'currentReadings' => [
                        'temperature' => Bmp::HIGH_TEMPERATURE_READING_BOUNDARY + 1,
                        'humidity' => Humidity::HIGH_READING + 1,
                        'latitude' => Latitude::HIGH_READING + 1,
                    ],
                ],
                [
                    'sensorType' => Bmp::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Bmp::NAME],
                    'currentReadings' => [
                        'temperature' => Bmp::LOW_TEMPERATURE_READING_BOUNDARY - 1,
                        'humidity' => Humidity::LOW_READING - 1,
                        'latitude' => Latitude::LOW_READING - 1,
                    ],
                ]
            ],
            'errors' => [
                'The lowest possible ' . Latitude::READING_TYPE . ' is ' . Latitude::LOW_READING . '° you entered ' . Latitude::LOW_READING - 1 . Latitude::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be under ' . Humidity::LOW_READING . Humidity::READING_SYMBOL . ' you entered ' . Humidity::LOW_READING - 1 . Humidity::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Bmp::NAME . ' sensor cannot be below ' . Bmp::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Bmp::LOW_TEMPERATURE_READING_BOUNDARY -1  . Temperature::READING_SYMBOL,
                'The highest possible ' . Latitude::READING_TYPE . ' is ' . Latitude::HIGH_READING . Latitude::READING_SYMBOL . ' you entered ' . Latitude::HIGH_READING + 1 . Latitude::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be over '. Humidity::HIGH_READING . Humidity::READING_SYMBOL .' you entered ' . Humidity::HIGH_READING + 1 . Humidity::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Bmp::NAME . ' sensor cannot exceed '. Bmp::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered '. Bmp::HIGH_TEMPERATURE_READING_BOUNDARY + 1 .Temperature::READING_SYMBOL,
            ],
        ];

        yield [
            'sensorData' => [
                [
                    'sensorType' => Soil::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => Soil::HIGH_SOIL_READING_BOUNDARY + 1,
                    ],
                ],
                [
                    'sensorType' => Soil::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Soil::NAME],
                    'currentReadings' => [
                        'analog' => Soil::LOW_SOIL_READING_BOUNDARY - 1,
                    ],
                ]
            ],
            'errors' => [
                'Reading for ' . Soil::NAME . ' sensor cannot be under ' . Soil::LOW_SOIL_READING_BOUNDARY . ' you entered ' . Soil::LOW_SOIL_READING_BOUNDARY - 1,
                'Reading for ' . Soil::NAME . ' sensor cannot be over ' . Soil::HIGH_SOIL_READING_BOUNDARY . ' you entered ' . Soil::HIGH_SOIL_READING_BOUNDARY + 1,
            ],
        ];

        //LDR
        yield [
            'sensorData' => [
                [
                    'sensorType' => LDR::NAME,
                    'sensorName' => SensorFixtures::SENSORS[LDR::NAME],
                    'currentReadings' => [
                        'analog' => LDR::HIGH_READING + 1,
                    ],
                ],
                [
                    'sensorType' => LDR::NAME,
                    'sensorName' => SensorFixtures::SENSORS[LDR::NAME],
                    'currentReadings' => [
                        'analog' => LDR::LOW_READING - 1,
                    ],
                ]
            ],
            'errors' => [
                'Reading for ' . LDR::NAME . ' sensor cannot be under ' . LDR::LOW_READING . ' you entered ' . LDR::LOW_READING - 1,
                'Reading for ' . LDR::NAME . ' sensor cannot be over ' . LDR::HIGH_READING . ' you entered ' . LDR::HIGH_READING + 1,
            ],
        ];

        // SHT
        yield [
            'sensorData' => [
                [
                    'sensorType' => Sht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Sht::NAME],
                    'currentReadings' => [
                        'temperature' => Sht::HIGH_TEMPERATURE_READING_BOUNDARY + 1,
                        'humidity' => Humidity::HIGH_READING + 1,
                    ],
                ],
                [
                    'sensorType' => Sht::NAME,
                    'sensorName' => SensorFixtures::SENSORS[Sht::NAME],
                    'currentReadings' => [
                        'temperature' => Sht::LOW_TEMPERATURE_READING_BOUNDARY - 1,
                        'humidity' => Humidity::LOW_READING - 1,
                    ],
                ]
            ],
            'errors' => [
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be under ' . Humidity::LOW_READING . Humidity::READING_SYMBOL . ' you entered ' . Humidity::LOW_READING - 1 . Humidity::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Sht::NAME . ' sensor cannot be below ' . Sht::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Sht::LOW_TEMPERATURE_READING_BOUNDARY -1  . Temperature::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be over '. Humidity::HIGH_READING . Humidity::READING_SYMBOL .' you entered ' . Humidity::HIGH_READING + 1 . Humidity::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Sht::NAME . ' sensor cannot exceed '. Sht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered '.Sht::HIGH_TEMPERATURE_READING_BOUNDARY + 1 . Temperature::READING_SYMBOL,
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
            self::ESP_SENSOR_UPDATE,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->deviceToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }
}
