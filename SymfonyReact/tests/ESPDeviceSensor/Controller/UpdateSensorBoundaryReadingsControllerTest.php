<?php

namespace App\Tests\ESPDeviceSensor\Controller;

use App\API\APIErrorMessages;
use App\Authentication\Controller\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateSensorBoundaryReadingsControllerTest extends WebTestCase
{
    private const UPDATE_SENSOR_BOUNDARY_READING_URL = '/HomeApp/api/user/sensors/boundary-update';

    private KernelBrowser $client;

    private ?string $userToken = null;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->setUserToken();
    }

    private function setUserToken(bool $forceToken = false, ?string $username = null, ?string $password = null): ?string
    {
        if ($this->userToken === null || $forceToken === true) {
            $username = $username ?? UserDataFixtures::ADMIN_USER;
            $password = $password ?? UserDataFixtures::ADMIN_PASSWORD;

            $this->client->request(
                Request::METHOD_POST,
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'. $username .'","password":"'. $password .'"}'
            );

            $requestResponse = $this->client->getResponse();
            $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            if ($forceToken === false) {
                $this->userToken = $responseData['token'];
            }

            return $responseData['token'];
        }

        return null;
    }

    /**
     * @dataProvider multiUpdateOneCorrectOneIncorrectDataProvider
     */
    public function test_multi_update_mixed_data(
        string $sensorType,
        string $tableId,
        array $sensorReadingsToUpdate,
        array|string $expectedDataPayloadMessage,
        array $expectedErrorPayloadMessage,
        string $expectedTitle,
        int $expectedStatusCode,
    ): void
    {
        $sensorTypeRepository = $this->entityManager->getRepository($sensorType);
        $sensorTypeObject = $sensorTypeRepository->findAll()[0];
        if ($sensorTypeObject instanceof StandardSensorTypeInterface) {
            $sensorData = [
                'sensorId' => $sensorTypeObject->getSensorObject()->getSensorNameID(),
                'sensorData' => $sensorReadingsToUpdate,
            ];
        }
        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            self::UPDATE_SENSOR_BOUNDARY_READING_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $title = $responseData['title'];
        $dataPayload = $responseData['payload'] ?? null;
        $errorsPayload = $responseData['errors'] ?? null;

        self::assertEquals($expectedStatusCode, $this->client->getResponse()->getStatusCode());
        self::assertEquals($expectedTitle, $title);
        if ($dataPayload !== null) {
            self::assertEquals($expectedDataPayloadMessage, $dataPayload);
        }
        if ($errorsPayload !== null) {
            self::assertEquals($expectedErrorPayloadMessage, $errorsPayload);
        }

        $sensorReadingTypeAfterUpdate = $sensorTypeRepository->findOneBy([$tableId => $sensorTypeObject->getSensorTypeID()]);

        $readingUpdates = [];
        foreach ($sensorReadingsToUpdate as $sensorReading) {
            $readingUpdates[$sensorReading['readingType']] = [
                'highReading' => $sensorReading['highReading'] ?? null,
                'lowReading' => $sensorReading['lowReading'] ?? null,
                'outOfBounds' => $sensorReading['outOfBounds'],
            ];
        }

        if ($sensorReadingTypeAfterUpdate instanceof TemperatureSensorTypeInterface) {
            $this->checkOutOfBoundResult($readingUpdates, $sensorReadingTypeAfterUpdate->getTempObject(), 'temperature');
        }
        if ($sensorReadingTypeAfterUpdate instanceof HumiditySensorTypeInterface) {
            $this->checkOutOfBoundResult($readingUpdates, $sensorReadingTypeAfterUpdate->getHumidObject(), 'humidity');
        }
        if ($sensorReadingTypeAfterUpdate instanceof AnalogSensorTypeInterface) {
            $this->checkOutOfBoundResult($readingUpdates, $sensorReadingTypeAfterUpdate->getAnalogObject(), 'analog');
        }
        if ($sensorReadingTypeAfterUpdate instanceof LatitudeSensorTypeInterface) {
            $this->checkOutOfBoundResult($readingUpdates, $sensorReadingTypeAfterUpdate->getLatitudeObject(), 'latitude');
        }
    }

    private function checkOutOfBoundResult(array $readingUpdates, AllSensorReadingTypeInterface $object, string $sensorReadingType): void
    {
        if ($object instanceof StandardReadingSensorInterface) {
            if ($readingUpdates[$sensorReadingType]['outOfBounds'] === true) {
                if (!empty($readingUpdates[$sensorReadingType]['highReading'])) {
                    self::assertNotEquals(
                        $readingUpdates[$sensorReadingType]['highReading'],
                        $object->getHighReading()
                    );
                }
                if (!empty($readingUpdates[$sensorReadingType]['lowReading'])) {
                    self::assertNotEquals(
                        $readingUpdates[$sensorReadingType]['lowReading'],
                        $object->getLowReading()
                    );
                }
            } else {
                if (!empty($readingUpdates[$sensorReadingType]['highReading'])) {
                    self::assertEquals(
                        $readingUpdates[$sensorReadingType]['highReading'],
                        $object->getHighReading()
                    );
                }
                if (!empty($readingUpdates[$sensorReadingType]['lowReading'])) {
                    self::assertEquals(
                        $readingUpdates[$sensorReadingType]['lowReading'],
                        $object->getLowReading()
                    );
                }
            }
        }
    }

    public function multiUpdateOneCorrectOneIncorrectDataProvider(): Generator
    {
//        DHT
        yield [
            'sensorType' => Dht::class,
            'tableId' => 'dhtID',
            'sensorReadingTypes' => [
                 [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'lowReading' => Dht::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING + 5,
                    'lowReading' => Humidity::LOW_READING - 5,
                    'outOfBounds' => true,
                ]
            ],
            'dataPayloadMessage' => [
                "successfullyUpdated" => [
                    "temperature"
                ]
            ],
            'errorsPayloadMessage' => [
                "Humidity for this sensor cannot be over 100 you entered 105%",
                "Humidity for this sensor cannot be under 0 you entered -5%"
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_MULTI_STATUS,
        ];

        yield [
            'sensorType' => Dht::class,
            'tableId' => 'dhtID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 5,
                    'lowReading' => Dht::LOW_TEMPERATURE_READING_BOUNDARY - 5,
                    'outOfBounds' => true,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING - 5,
                    'lowReading' => Humidity::LOW_READING + 5,
                    'outOfBounds' => false,
                ]
            ],
            'dataPayloadMessage' => [
                "successfullyUpdated" => [
                    "humidity"
                ]
            ],
            'errorsPayloadMessage' => [
                "Temperature settings for Dht sensor cannot exceed 80°C you entered 85°C",
                "Temperature settings for Dht sensor cannot be below -40°C you entered -45°C"
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_MULTI_STATUS,
        ];

        yield [
            'sensorType' => Dht::class,
            'tableId' => 'dhtID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 5,
                    'lowReading' => Dht::LOW_TEMPERATURE_READING_BOUNDARY - 5,
                    'outOfBounds' => true,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING + 5,
                    'lowReading' => Humidity::LOW_READING - 5,
                    'outOfBounds' => true,
                ]
            ],
            'dataPayloadMessage' => [],
            'errorsPayloadMessage' => [
                "Temperature settings for Dht sensor cannot exceed 80°C you entered 85°C",
                "Temperature settings for Dht sensor cannot be below -40°C you entered -45°C",
                "Humidity for this sensor cannot be over 100 you entered 105%",
                "Humidity for this sensor cannot be under 0 you entered -5%",
            ],
            'expectedTitle' => 'All sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_BAD_REQUEST,
        ];

        yield [
            'sensorType' => Dht::class,
            'tableId' => 'dhtID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'lowReading' => Dht::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING - 5,
                    'outOfBounds' => false,
                ]
            ],
            'dataPayloadMessage' => 'No Response Message',
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

        yield [
            'sensorType' => Dht::class,
            'tableId' => 'dhtID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY -5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'lowReading' => Humidity::LOW_READING + 5,
                    'outOfBounds' => false,
                ]
            ],
            'dataPayloadMessage' => 'No Response Message',
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

        yield [
            'sensorType' => Dht::class,
            'tableId' => 'dhtID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY,
                    'lowReading' => Dht::LOW_TEMPERATURE_READING_BOUNDARY,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING,
                    'lowReading' => Humidity::LOW_READING,
                    'outOfBounds' => false,
                ]
            ],
            'dataPayloadMessage' => 'No Response Message',
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

//        DALLAS
        yield [
            'sensorType' => Dallas::class,
            'tableId' => 'dallasID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dallas::HIGH_TEMPERATURE_READING_BOUNDARY + 5,
                    'lowReading' => Dallas::LOW_TEMPERATURE_READING_BOUNDARY - 5,
                    'outOfBounds' => true,
                ],
            ],
            'dataPayloadMessage' => [],
            'errorsPayloadMessage' => [
                "Temperature settings for Dallas sensor cannot exceed 125°C you entered 130°C",
                "Temperature settings for Dallas sensor cannot be below -55°C you entered -60°C",
            ],
            'expectedTitle' => 'All sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_BAD_REQUEST,
        ];

        yield [
            'sensorType' => Dallas::class,
            'tableId' => 'dallasID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dallas::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'outOfBounds' => false,
                ],
            ],
            'dataPayloadMessage' => 'No Response Message',
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

        yield [
            'sensorType' => Dallas::class,
            'tableId' => 'dallasID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'lowReading' => Dallas::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ],
            ],
            'dataPayloadMessage' => 'No Response Message',
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

        yield [
            'sensorType' => Dallas::class,
            'tableId' => 'dallasID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dallas::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'lowReading' => Dallas::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ],
            ],
            'dataPayloadMessage' => 'No Response Message',
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

//        BMP
        yield [
            'sensorType' => Bmp::class,
            'tableId' => 'bmpID',
            'sensorReadingTypes' => [
                 [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Bmp::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'lowReading' => Bmp::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING + 5,
                    'lowReading' => Humidity::LOW_READING - 5,
                    'outOfBounds' => true,
                ],
                [
                    'readingType' => Latitude::READING_TYPE,
                    'highReading' => Latitude::HIGH_READING - 5,
                    'lowReading' => Latitude::LOW_READING + 5,
                    'outOfBounds' => false,
                ]
            ],
            'dataPayloadMessage' => [
                "successfullyUpdated" => [
                    "temperature",
                    "latitude"
                ]
            ],
            'errorsPayloadMessage' => [
                "Humidity for this sensor cannot be over 100 you entered 105%",
                "Humidity for this sensor cannot be under 0 you entered -5%",
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_MULTI_STATUS,
        ];

        yield [
            'sensorType' => Bmp::class,
            'tableId' => 'bmpID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Bmp::HIGH_TEMPERATURE_READING_BOUNDARY + 5,
                    'lowReading' => Bmp::LOW_TEMPERATURE_READING_BOUNDARY - 5,
                    'outOfBounds' => true,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING - 5,
                    'lowReading' => Humidity::LOW_READING + 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Latitude::READING_TYPE,
                    'highReading' => Latitude::HIGH_READING - 5,
                    'lowReading' => Latitude::LOW_READING + 5,
                    'outOfBounds' => false,
                ]
            ],
            'dataPayloadMessage' => [
                "successfullyUpdated" => [
                    "humidity",
                    "latitude"
                ]
            ],
            'errorsPayloadMessage' => [
                "Temperature settings for Bmp sensor cannot exceed 85°C you entered 90°C",
                "Temperature settings for Bmp sensor cannot be below -45°C you entered -50°C"
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_MULTI_STATUS,
        ];


        yield [
            'sensorType' => Bmp::class,
            'tableId' => 'bmpID',
            'sensorReadingTypes' => [
                 [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Bmp::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'lowReading' => Bmp::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING + 5,
                    'lowReading' => Humidity::LOW_READING - 5,
                    'outOfBounds' => true,
                ],
                [
                    'readingType' => Latitude::READING_TYPE,
                    'highReading' => Latitude::HIGH_READING - 5,
                    'lowReading' => Latitude::LOW_READING + 5,
                    'outOfBounds' => false,
                ]
            ],
            'dataPayloadMessage' => [
                "successfullyUpdated" => [
                    "temperature",
                    "latitude"
                ]
            ],
            'errorsPayloadMessage' => [
                "Humidity for this sensor cannot be over 100 you entered 105%",
                "Humidity for this sensor cannot be under 0 you entered -5%"
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_MULTI_STATUS,
        ];

        yield [
            'sensorType' => Bmp::class,
            'tableId' => 'bmpID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Bmp::HIGH_TEMPERATURE_READING_BOUNDARY,
                    'lowReading' => Bmp::LOW_TEMPERATURE_READING_BOUNDARY,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING - 5,
                    'lowReading' => Humidity::LOW_READING + 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Latitude::READING_TYPE,
                    'highReading' => Latitude::HIGH_READING + 5,
                    'lowReading' => Latitude::LOW_READING - 5,
                    'outOfBounds' => true,
                ]
            ],
            'dataPayloadMessage' => [
                "successfullyUpdated" => [
                    "temperature",
                    "humidity",
                ]
            ],
            'errorsPayloadMessage' => [
                "The highest possible latitude is 90 you entered 95",
                "The lowest possible latitude is -90 you entered -95"
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_MULTI_STATUS,
        ];

        yield [
            'sensorType' => Bmp::class,
            'tableId' => 'bmpID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Bmp::HIGH_TEMPERATURE_READING_BOUNDARY + 5,
                    'lowReading' => Bmp::LOW_TEMPERATURE_READING_BOUNDARY - 5,
                    'outOfBounds' => true,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING + 5,
                    'lowReading' => Humidity::LOW_READING - 5,
                    'outOfBounds' => true,
                ],
                [
                    'readingType' => Latitude::READING_TYPE,
                    'highReading' => Latitude::HIGH_READING + 5,
                    'lowReading' => Latitude::LOW_READING - 5,
                    'outOfBounds' => true,
                ]
            ],
            'dataPayloadMessage' => [],
            'errorsPayloadMessage' => [
                "Temperature settings for Bmp sensor cannot exceed 85°C you entered 90°C",
                "Temperature settings for Bmp sensor cannot be below -45°C you entered -50°C",
                "Humidity for this sensor cannot be over 100 you entered 105%",
                "Humidity for this sensor cannot be under 0 you entered -5%",
                "The highest possible latitude is 90 you entered 95",
                "The lowest possible latitude is -90 you entered -95",
            ],
            'expectedTitle' => 'All sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_BAD_REQUEST,
        ];

        yield [
            'sensorType' => Bmp::class,
            'tableId' => 'bmpID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Bmp::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'lowReading' => Bmp::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING - 5,
                    'lowReading' => Humidity::LOW_READING + 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Latitude::READING_TYPE,
                    'highReading' => Latitude::HIGH_READING - 5,
                    'lowReading' => Latitude::LOW_READING + 5,
                    'outOfBounds' => false,
                ]
            ],
            'dataPayloadMessage' => 'No Response Message',
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

        yield [
            'sensorType' => Bmp::class,
            'tableId' => 'bmpID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Bmp::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'highReading' => Humidity::HIGH_READING - 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Latitude::READING_TYPE,
                    'highReading' => Latitude::HIGH_READING - 5,
                    'outOfBounds' => false,
                ]
            ],
            'dataPayloadMessage' => 'No Response Message',
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

        yield [
            'sensorType' => Bmp::class,
            'tableId' => 'bmpID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'lowReading' => Bmp::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Humidity::READING_TYPE,
                    'lowReading' => Humidity::LOW_READING + 5,
                    'outOfBounds' => false,
                ],
                [
                    'readingType' => Latitude::READING_TYPE,
                    'lowReading' => Latitude::LOW_READING + 5,
                    'outOfBounds' => false,
                ]
            ],
            'dataPayloadMessage' => 'No Response Message',
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

      // SOIL
        yield [
            'sensorType' => Soil::class,
            'tableId' => 'soilID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Analog::READING_TYPE,
                    'highReading' => Soil::HIGH_SOIL_READING_BOUNDARY - 5,
                    'lowReading' => Soil::LOW_SOIL_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ],
            ],
            'dataPayloadMessage' => "No Response Message",
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

        yield [
            'sensorType' => Soil::class,
            'tableId' => 'soilID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Analog::READING_TYPE,
                    'lowReading' => Soil::LOW_SOIL_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ],
            ],
            'dataPayloadMessage' => "No Response Message",
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

        yield [
            'sensorType' => Soil::class,
            'tableId' => 'soilID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Analog::READING_TYPE,
                    'highReading' => Soil::HIGH_SOIL_READING_BOUNDARY - 5,
                    'outOfBounds' => false,
                ],
            ],
            'dataPayloadMessage' => "No Response Message",
            'errorsPayloadMessage' => [],
            'expectedTitle' => 'Request Successful',
            'expectedStatusCode' => Response::HTTP_ACCEPTED,
        ];

        yield [
            'sensorType' => Soil::class,
            'tableId' => 'soilID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Analog::READING_TYPE,
                    'highReading' => Soil::HIGH_SOIL_READING_BOUNDARY + 5,
                    'lowReading' => Soil::LOW_SOIL_READING_BOUNDARY - 5,
                    'outOfBounds' => true,
                ],
            ],
            'dataPayloadMessage' => [],
            'errorsPayloadMessage' => [
                "Reading for this sensor cannot be over 9999 you entered 10004",
                "Reading for this sensor cannot be under 1000 you entered 995"
            ],
            'expectedTitle' => 'All sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_BAD_REQUEST,
        ];
    }

    public function test_sending_malformed_request(): void
    {
        $this->client->request(
            Request::METHOD_PUT,
            self::UPDATE_SENSOR_BOUNDARY_READING_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/form-data'],
            'readingTypes' . Analog::READING_TYPE . '&highReading=' . Soil::HIGH_SOIL_READING_BOUNDARY .'&lowReading=' . Soil::LOW_SOIL_READING_BOUNDARY
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals("Bad Request No Data Returned", $responseData['title']);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals('Format not supported', $responseData['errors'][0]);
    }

    /**
     * @dataProvider sendingEmptyDataSetsDataProvider
     */
    public function test_sending_empty_sensor_data(array $sensorDataToSend): void
    {
        $sensorTypeRepository = $this->entityManager->getRepository(Dht::class);
        $sensorTypeObject = $sensorTypeRepository->findAll()[0];

        if ($sensorTypeObject instanceof StandardSensorTypeInterface) {
            $sensorData = [
                $sensorDataToSend
            ];
        }
        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            self::UPDATE_SENSOR_BOUNDARY_READING_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals(APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA, $responseData['errors'][0]);
    }

    public function sendingEmptyDataSetsDataProvider(): Generator
    {
        yield [
            'sensorData' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'lowReading' => Dht::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ]
            ]
        ];

        yield [
            ['sensorId' => 1]
        ];
    }

    public function test_sending_wrong_sensor_id(): void
    {
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);
        while (true) {
            $wrongSensorId = random_int(1, 10000);
            $sensorTypeObject = $sensorRepository->findOneBy(['sensorNameID' => $wrongSensorId]);

            if (!$sensorTypeObject instanceof StandardSensorTypeInterface) {
                break;
            }
        }

        $sensorData = [
            'sensorId' => $wrongSensorId,
            'sensorData' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'lowReading' => Dht::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ]
            ]
        ];

        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            self::UPDATE_SENSOR_BOUNDARY_READING_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals(
            sprintf(
                APIErrorMessages::OBJECT_NOT_FOUND,
                'Sensor',
            ),
            $responseData['errors'][0]
        );
    }

    public function test_sending_request_not_recognized_sensor_type(): void
    {
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $sensorTypeObject = $sensorRepository->findAll()[0];

        $readingType = 'total-random-string';
        $sensorData = [
            'sensorId' => $sensorTypeObject->getSensorNameID(),
            'sensorData' => [
                [
                    'readingType' => $readingType,
                    'highReading' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'lowReading' => Dht::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ]
            ]
        ];

        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            self::UPDATE_SENSOR_BOUNDARY_READING_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('All sensor boundary update requests failed', $responseData['title']);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals($readingType . ' Sensor type not found', $responseData['errors'][0]
        );
    }

    public function test_sending_request_for_sensor_user_not_apart_of_group(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $loggedInUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $userNotInGroup = $userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER]);

        $deviceRepository = $this->entityManager->getRepository(Devices::class);
        $deviceObject = $deviceRepository->findBy(['groupNameID' => $loggedInUser->getGroupNameID()])[0];

        if (in_array($deviceObject->getGroupNameObject()->getGroupNameID(), $userNotInGroup->getGroupNameIds(), true)) {
            throw new \Exception();
        }

        $sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $sensorObjectLoggedInUser = $sensorRepository->findBy(['deviceNameID' => $deviceObject->getDeviceNameID()])[0];

        $sensorData = [
            'sensorId' => $sensorObjectLoggedInUser->getSensorNameID(),
            'sensorData' => [
                [
                    'readingType' => Temperature::READING_TYPE,
                    'highReading' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 5,
                    'lowReading' => Dht::LOW_TEMPERATURE_READING_BOUNDARY + 5,
                    'outOfBounds' => false,
                ]
            ]
        ];

        $jsonData = json_encode($sensorData);

        $token = $this->setUserToken(true,  UserDataFixtures::REGULAR_USER, UserDataFixtures::REGULAR_PASSWORD  );
        $this->client->request(
            Request::METHOD_PUT,
            self::UPDATE_SENSOR_BOUNDARY_READING_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $token, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
        self::assertEquals("You have been denied permission to perform this action", $responseData['errors'][0]);
    }
}
