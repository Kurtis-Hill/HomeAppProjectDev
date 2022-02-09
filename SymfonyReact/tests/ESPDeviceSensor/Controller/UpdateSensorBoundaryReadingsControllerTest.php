<?php

namespace App\Tests\ESPDeviceSensor\Controller;

use App\Authentication\Controller\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
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

    private function setUserToken(bool $forceToken = false): ?string
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
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
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
                'highReading' => $sensorReading['highReading'],
                'lowReading' => $sensorReading['lowReading'],
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
                self::assertNotEquals(
                    $readingUpdates[$sensorReadingType]['highReading'],
                    $object->getHighReading()
                );
                self::assertNotEquals(
                    $readingUpdates[$sensorReadingType]['lowReading'],
                    $object->getLowReading()
                );
            } else {
                self::assertEquals(
                    $readingUpdates[$sensorReadingType]['highReading'],
                    $object->getHighReading()
                );
                self::assertEquals(
                    $readingUpdates[$sensorReadingType]['lowReading'],
                    $object->getLowReading()
                );
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
                "Successfully updated sensor boundary readings" => [
                    "temperature"
                ]
            ],
            'errorsPayloadMessage' => [
                "humidity" => [
                    "Humidity for this sensor cannot be over 100 you entered 105%",
                    "Humidity for this sensor cannot be under 0 you entered -5%"
                ]
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
                "Successfully updated sensor boundary readings" => [
                    "humidity"
                ]
            ],
            'errorsPayloadMessage' => [
                "temperature" => [
                    "Temperature settings for Dht sensor cannot exceed 80°C you entered 85°C",
                    "Temperature settings for Dht sensor cannot be below -40°C you entered -45°C"
                ]
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
                "temperature" => [
                    "Temperature settings for Dht sensor cannot exceed 80°C you entered 85°C",
                    "Temperature settings for Dht sensor cannot be below -40°C you entered -45°C"
                ],
                "humidity" => [
                    "Humidity for this sensor cannot be over 100 you entered 105%",
                    "Humidity for this sensor cannot be under 0 you entered -5%"
                ]
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
                "temperature" => [
                    "Temperature settings for Dallas sensor cannot exceed 125°C you entered 130°C",
                    "Temperature settings for Dallas sensor cannot be below -55°C you entered -60°C"
                ]
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
                "Successfully updated sensor boundary readings" => [
                    "temperature",
                    "latitude"
                ]
            ],
            'errorsPayloadMessage' => [
                "humidity" => [
                    "Humidity for this sensor cannot be over 100 you entered 105%",
                    "Humidity for this sensor cannot be under 0 you entered -5%"
                ]
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
                "Successfully updated sensor boundary readings" => [
                    "humidity",
                    "latitude"
                ]
            ],
            'errorsPayloadMessage' => [
                "temperature" => [
                    "Temperature settings for Bmp sensor cannot exceed 85°C you entered 90°C",
                    "Temperature settings for Bmp sensor cannot be below -45°C you entered -50°C"
                ]
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
                "Successfully updated sensor boundary readings" => [
                    "temperature",
                    "latitude"
                ]
            ],
            'errorsPayloadMessage' => [
                "humidity" => [
                    "Humidity for this sensor cannot be over 100 you entered 105%",
                    "Humidity for this sensor cannot be under 0 you entered -5%"
                ]
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
                "Successfully updated sensor boundary readings" => [
                    "temperature",
                    "humidity",
                ]
            ],
            'errorsPayloadMessage' => [
                "latitude" => [
                    "The highest possible latitude is 90 you entered 95",
                    "The lowest possible latitude is -90 you entered -95"
                ],
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
                "latitude" => [
                    "The highest possible latitude is 90 you entered 95",
                    "The lowest possible latitude is -90 you entered -95"
                ],
                "temperature" => [
                    "Temperature settings for Bmp sensor cannot exceed 85°C you entered 90°C",
                    "Temperature settings for Bmp sensor cannot be below -45°C you entered -50°C"
                ],
                "humidity" => [
                    "Humidity for this sensor cannot be over 100 you entered 105%",
                    "Humidity for this sensor cannot be under 0 you entered -5%"
                ]
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
                    'highReading' => Soil::HIGH_SOIL_READING_BOUNDARY + 5,
                    'lowReading' => Soil::LOW_SOIL_READING_BOUNDARY - 5,
                    'outOfBounds' => true,
                ],
            ],
            'dataPayloadMessage' => [],
            'errorsPayloadMessage' => [
                "analog" => [
                    "Reading for this sensor cannot be over 9999 you entered 10004",
                    "Reading for this sensor cannot be under 1000 you entered 995"
                ]
            ],
            'expectedTitle' => 'All sensor boundary update requests failed',
            'expectedStatusCode' => Response::HTTP_BAD_REQUEST,
        ];
    }


}
