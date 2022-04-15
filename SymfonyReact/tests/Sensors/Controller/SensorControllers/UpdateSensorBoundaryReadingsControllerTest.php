<?php

namespace Sensors\Controller\SensorControllers;

use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\Common\API\APIErrorMessages;
use App\Devices\Entity\Devices;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateSensorBoundaryReadingsControllerTest extends WebTestCase
{
    private const UPDATE_SENSOR_BOUNDARY_READING_URL = '/HomeApp/api/user/sensor/%d/boundary-update';

    private KernelBrowser $client;

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

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
    ): void {
        $sensorTypeRepository = $this->entityManager->getRepository($sensorType);
        $sensorTypeObject = $sensorTypeRepository->findAll()[0];
        if ($sensorTypeObject instanceof StandardSensorTypeInterface) {
            $sensorData = [
                'sensorData' => $sensorReadingsToUpdate,
            ];
        }
        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorTypeObject->getSensorObject()->getSensorNameID()),
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

        self::assertEquals(Response::HTTP_MULTI_STATUS, $this->client->getResponse()->getStatusCode());
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
                    Temperature::READING_TYPE
                ]
            ],
            'errorsPayloadMessage' => [
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be over ' . Humidity::HIGH_READING . Humidity::READING_SYMBOL . ' you entered '. Humidity::HIGH_READING + 5 . Humidity::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be under '. Humidity::LOW_READING . Humidity::READING_SYMBOL . ' you entered ' . Humidity::LOW_READING - 5 . Humidity::READING_SYMBOL,
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
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
                    Humidity::READING_TYPE
                ]
            ],
            'errorsPayloadMessage' => [
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Dht::NAME .' sensor cannot exceed ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 5 . Temperature::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) .' settings for ' . Dht::NAME . ' sensor cannot be below ' . Dht::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Dht::LOW_TEMPERATURE_READING_BOUNDARY - 5 . Temperature::READING_SYMBOL,
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
        ];
//        DALLAS



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
                    Temperature::READING_TYPE,
                    "latitude"
                ]
            ],
            'errorsPayloadMessage' => [
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be over ' . Humidity::HIGH_READING . Humidity::READING_SYMBOL . ' you entered '. Humidity::HIGH_READING + 5 . Humidity::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be under '. Humidity::LOW_READING . Humidity::READING_SYMBOL . ' you entered ' . Humidity::LOW_READING - 5 . Humidity::READING_SYMBOL,
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
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
                    Humidity::READING_TYPE,
                    Latitude::READING_TYPE
                ]
            ],
            'errorsPayloadMessage' => [
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Bmp::NAME . ' sensor cannot exceed ' . Bmp::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Bmp::HIGH_TEMPERATURE_READING_BOUNDARY + 5 . Temperature::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Bmp::NAME .' sensor cannot be below ' . Bmp::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Bmp::LOW_TEMPERATURE_READING_BOUNDARY - 5 . Temperature::READING_SYMBOL,
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
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
                    Temperature::READING_TYPE,
                    Latitude::READING_TYPE
                ]
            ],
            'errorsPayloadMessage' => [
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be over ' . Humidity::HIGH_READING . Humidity::READING_SYMBOL . ' you entered '. Humidity::HIGH_READING + 5 . Humidity::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be under '. Humidity::LOW_READING . Humidity::READING_SYMBOL . ' you entered ' . Humidity::LOW_READING - 5 . Humidity::READING_SYMBOL,
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
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
                    Temperature::READING_TYPE,
                    Humidity::READING_TYPE,
                ]
            ],
            'errorsPayloadMessage' => [
                'The highest possible ' . Latitude::READING_TYPE .' is ' . Latitude::HIGH_READING . Latitude::READING_SYMBOL . ' you entered ' . Latitude::HIGH_READING + 5 . Latitude::READING_SYMBOL,
                'The lowest possible '. Latitude::READING_TYPE .' is ' . Latitude::LOW_READING . Latitude::READING_SYMBOL .' you entered ' . Latitude::LOW_READING - 5 . Latitude::READING_SYMBOL
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
        ];


        // SOIL
    }

    /**
     * @dataProvider correctUpdateDataDataProvider
     */
    public function test_correct_update_data_returns_accepted(
        string $sensorType,
        string $tableId,
        array $sensorReadingsToUpdate,
        array|string $expectedDataPayloadMessage,
        string $expectedTitle,
    ): void {
        $sensorTypeRepository = $this->entityManager->getRepository($sensorType);
        $sensorTypeObject = $sensorTypeRepository->findAll()[0];
        if ($sensorTypeObject instanceof StandardSensorTypeInterface) {
            $sensorData = [
                'sensorData' => $sensorReadingsToUpdate,
            ];
        }
        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorTypeObject->getSensorObject()->getSensorNameID()),
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

        self::assertEquals(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());
        self::assertEquals($expectedTitle, $title);
        if ($dataPayload !== null) {
            self::assertEquals($expectedDataPayloadMessage, $dataPayload);
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


    public function correctUpdateDataDataProvider(): Generator
    {
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
            'expectedTitle' => 'Request Successful',
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
            'expectedTitle' => 'Request Successful',
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
            'expectedTitle' => 'Request Successful',
        ];

        //Dallas
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
            'expectedTitle' => 'Request Successful',
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
            'expectedTitle' => 'Request Successful',
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
            'expectedTitle' => 'Request Successful',
        ];

        //Bmp
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
            'expectedTitle' => 'Request Successful',
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
            'expectedTitle' => 'Request Successful',
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
            'expectedTitle' => 'Request Successful',
        ];

        // Soil
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
            'expectedTitle' => 'Request Successful',
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
            'expectedTitle' => 'Request Successful',
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
            'expectedTitle' => 'Request Successful',
        ];
    }

    /**
     * @dataProvider sendingEntireWrongReadingPayloadDataProvider
     */
    public function test_sending_wrong_data_entire_reading_payload(
        string $sensorType,
        string $tableId,
        array $sensorReadingsToUpdate,
        array|string $expectedDataPayloadMessage,
        array $expectedErrorPayloadMessage,
        string $expectedTitle,
    ): void {
        $sensorTypeRepository = $this->entityManager->getRepository($sensorType);
        $sensorTypeObject = $sensorTypeRepository->findAll()[0];
        if ($sensorTypeObject instanceof StandardSensorTypeInterface) {
            $sensorData = [
                'sensorData' => $sensorReadingsToUpdate,
            ];
        }
        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorTypeObject->getSensorObject()->getSensorNameID()),
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

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
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

    public function sendingEntireWrongReadingPayloadDataProvider(): Generator
    {
//        DHT
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
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Dht::NAME .' sensor cannot exceed ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 5 . Temperature::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) .' settings for ' . Dht::NAME . ' sensor cannot be below ' . Dht::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Dht::LOW_TEMPERATURE_READING_BOUNDARY - 5 . Temperature::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be over ' . Humidity::HIGH_READING . Humidity::READING_SYMBOL . ' you entered '. Humidity::HIGH_READING + 5 . Humidity::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be under '. Humidity::LOW_READING . Humidity::READING_SYMBOL . ' you entered ' . Humidity::LOW_READING - 5 . Humidity::READING_SYMBOL,
            ],
            'expectedTitle' => 'All sensor boundary update requests failed',
        ];

        //Dallas
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
        ];

        // BMP
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
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Bmp::NAME . ' sensor cannot exceed ' . Bmp::HIGH_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Bmp::HIGH_TEMPERATURE_READING_BOUNDARY + 5 . Temperature::READING_SYMBOL,
                ucfirst(Temperature::READING_TYPE) . ' settings for ' . Bmp::NAME .' sensor cannot be below ' . Bmp::LOW_TEMPERATURE_READING_BOUNDARY . Temperature::READING_SYMBOL . ' you entered ' . Bmp::LOW_TEMPERATURE_READING_BOUNDARY - 5 . Temperature::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be over ' . Humidity::HIGH_READING . Humidity::READING_SYMBOL . ' you entered '. Humidity::HIGH_READING + 5 . Humidity::READING_SYMBOL,
                ucfirst(Humidity::READING_TYPE) . ' for this sensor cannot be under '. Humidity::LOW_READING . Humidity::READING_SYMBOL . ' you entered ' . Humidity::LOW_READING - 5 . Humidity::READING_SYMBOL,
                'The highest possible ' . Latitude::READING_TYPE .' is ' . Latitude::HIGH_READING . Latitude::READING_SYMBOL . ' you entered ' . Latitude::HIGH_READING + 5 . Latitude::READING_SYMBOL,
                'The lowest possible '. Latitude::READING_TYPE .' is ' . Latitude::LOW_READING . Latitude::READING_SYMBOL .' you entered ' . Latitude::LOW_READING - 5 . Latitude::READING_SYMBOL,
            ],
            'expectedTitle' => 'All sensor boundary update requests failed',
        ];

        // Soil

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
                'Reading for ' . Soil::NAME . ' sensor cannot be over ' . Soil::HIGH_SOIL_READING_BOUNDARY .' you entered ' . Soil::HIGH_SOIL_READING_BOUNDARY + 5,
                'Reading for ' . Soil::NAME . ' sensor cannot be under ' . Soil::LOW_SOIL_READING_BOUNDARY . ' you entered ' . Soil::LOW_SOIL_READING_BOUNDARY - 5,
            ],
            'expectedTitle' => 'All sensor boundary update requests failed',
        ];
    }

    public function test_sending_malformed_request(): void
    {
        $sensorObject = $this->entityManager->getRepository(Sensor::class)->findAll()[0];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorObject->getSensorNameID()),
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
        self::assertEquals(APIErrorMessages::FORMAT_NOT_SUPPORTED, $responseData['errors'][0]);
    }

    /**
     * @dataProvider sendingMissingDataSetsDataProvider
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
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorTypeObject->getSensorObject()->getSensorNameID()),
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
        self::assertEquals('sensorData cannot be empty', $responseData['errors'][0]);
    }

    public function sendingMissingDataSetsDataProvider(): Generator
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
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $wrongSensorId),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_request_not_recognized_sensor_type(): void
    {
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $sensorTypeObject = $sensorRepository->findAll()[0];

        $readingType = 'total-random-string';
        $sensorData = [
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
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorTypeObject->getSensorNameID()),
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
        self::assertEquals($readingType . ' Sensor type not recognised', $responseData['errors'][0]);
    }

    public function test_sending_request_for_sensor_user_not_apart_of_group(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $loggedInUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $userNotInGroup = $userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER]);

        $deviceRepository = $this->entityManager->getRepository(Devices::class);
        $deviceObject = $deviceRepository->findBy(['groupNameID' => $loggedInUser->getGroupNameID()])[0];

        if (in_array($deviceObject->getGroupNameObject()->getGroupNameID(), $userNotInGroup->getGroupNameIds(), true)) {
            throw new Exception();
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

        $token = $this->setUserToken(true, UserDataFixtures::REGULAR_USER, UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorObjectLoggedInUser->getSensorNameID()),
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
        self::assertEquals(APIErrorMessages::ACCESS_DENIED, $responseData['errors'][0]);
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
}