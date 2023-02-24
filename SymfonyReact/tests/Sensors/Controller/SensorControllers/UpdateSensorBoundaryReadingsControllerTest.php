<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Common\API\APIErrorMessages;
use App\Devices\Entity\Devices;
use App\Sensors\Controller\SensorControllers\UpdateSensorBoundaryReadingsController;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Tests\Traits\TestLoginTrait;
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
    use TestLoginTrait;

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

        $this->userToken = $this->setUserToken($this->client);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider multiUpdateOneCorrectOneIncorrectDataProvider
     */
    public function test_multi_update_mixed_data(
        string $sensorType,
        string $tableId,
        array $sensorReadingsToUpdate,
        array $expectedErrorPayloadMessage,
        string $expectedTitle,
    ): void {
        $sensorTypeRepository = $this->entityManager->getRepository($sensorType);
        /** @var SensorTypeInterface $sensorTypeObject */
        $sensorTypeObject = $sensorTypeRepository->findAll()[0];
        if ($sensorTypeObject instanceof StandardSensorTypeInterface) {
            $sensorData = [
                'sensorData' => $sensorReadingsToUpdate,
            ];
        }
        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorTypeObject->getSensor()->getSensorID()),
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
        $dataPayloads = $responseData['payload'];
        $errorsPayload = $responseData['errors'];

        self::assertEquals(Response::HTTP_MULTI_STATUS, $this->client->getResponse()->getStatusCode());
        self::assertEquals($expectedTitle, $title);


        if ($errorsPayload !== null) {
            self::assertEquals($expectedErrorPayloadMessage, $errorsPayload);
        }

        if ($dataPayloads !== null) {
            foreach ($dataPayloads as $dataPayload) {
                self::assertArrayHasKey('sensorReadingTypeID', $dataPayload);
                self::assertArrayHasKey('readingType', $dataPayload);
                self::assertArrayHasKey('highReading', $dataPayload);
                self::assertArrayHasKey('lowReading', $dataPayload);
                self::assertArrayHasKey('constRecord', $dataPayload);
            }
        }

        $sensorTypeAfterUpdate = $sensorTypeRepository->findOneBy([$tableId => $sensorTypeObject->getSensorTypeID()]);

        $readingUpdates = [];
        foreach ($sensorReadingsToUpdate as $sensorReading) {
            $readingUpdates[$sensorReading['readingType']] = [
                'highReading' => $sensorReading['highReading'] ?? null,
                'lowReading' => $sensorReading['lowReading'] ?? null,
                'outOfBounds' => $sensorReading['outOfBounds'],
            ];
        }

        if ($sensorTypeAfterUpdate instanceof TemperatureSensorTypeInterface) {
            $this->checkOutOfBoundResult($readingUpdates, $sensorTypeAfterUpdate->getTemperature(), 'temperature');
        }
        if ($sensorTypeAfterUpdate instanceof HumiditySensorTypeInterface) {
            $this->checkOutOfBoundResult($readingUpdates, $sensorTypeAfterUpdate->getHumidObject(), 'humidity');
        }
        if ($sensorTypeAfterUpdate instanceof AnalogSensorTypeInterface) {
            $this->checkOutOfBoundResult($readingUpdates, $sensorTypeAfterUpdate->getAnalogObject(), 'analog');
        }
        if ($sensorTypeAfterUpdate instanceof LatitudeSensorTypeInterface) {
            $this->checkOutOfBoundResult($readingUpdates, $sensorTypeAfterUpdate->getLatitudeObject(), 'latitude');
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
            'errorsPayloadMessage' => [
                'The highest possible ' . Latitude::READING_TYPE .' is ' . Latitude::HIGH_READING . Latitude::READING_SYMBOL . ' you entered ' . Latitude::HIGH_READING + 5 . Latitude::READING_SYMBOL,
                'The lowest possible '. Latitude::READING_TYPE .' is ' . Latitude::LOW_READING . Latitude::READING_SYMBOL .' you entered ' . Latitude::LOW_READING - 5 . Latitude::READING_SYMBOL
            ],
            'expectedTitle' => 'Some sensor boundary update requests failed',
        ];
        // No SOIL Doesnt have multiple readings types
    }

    /**
     * @dataProvider correctUpdateDataDataProvider
     */
    public function test_correct_update_data_returns_accepted(
        string $sensorType,
        string $tableId,
        array $sensorReadingsToUpdate,
        array $expectedDataPayloadMessage,
        string $expectedTitle,
    ): void {
        $sensorTypeRepository = $this->entityManager->getRepository($sensorType);
        /** @var StandardReadingSensorInterface $sensorReadingTypeObject */
        $sensorReadingTypeObject = $sensorTypeRepository->findAll()[0];
        if ($sensorReadingTypeObject instanceof StandardSensorTypeInterface) {
            $sensorData = [
                'sensorData' => $sensorReadingsToUpdate,
            ];
        }
        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorReadingTypeObject->getSensor()->getSensorID()),
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
        $sensorReadingTypeObject = $sensorTypeRepository->findAll()[0];
        if (!isset($responseData['payload'])) {
            self::fail('Payload not set in response');
        }
        $title = $responseData['title'];
        $dataPayloads = $responseData['payload'];

        $count = 0;
        foreach ($dataPayloads as $dataPayload) {
            self::assertEquals($expectedDataPayloadMessage[$count]['readingType'], $dataPayload['readingType']);

            if ($sensorReadingTypeObject instanceof StandardSensorTypeInterface) {
                self::assertEquals($expectedDataPayloadMessage[$count]['highReading'], $dataPayload['highReading']);
                self::assertEquals($expectedDataPayloadMessage[$count]['lowReading'], $dataPayload['lowReading']);
                self::assertEquals($expectedDataPayloadMessage[$count]['constRecord'], $dataPayload['constRecord']);
            }
            ++$count;
        }
        self::assertEquals(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());
        self::assertEquals($expectedTitle, $title);

        $sensorReadingTypeAfterUpdate = $sensorTypeRepository->findOneBy([$tableId => $sensorReadingTypeObject->getSensorTypeID()]);

        $readingUpdates = [];
        foreach ($sensorReadingsToUpdate as $sensorReading) {
            $readingUpdates[$sensorReading['readingType']] = [
                'highReading' => $sensorReading['highReading'] ?? null,
                'lowReading' => $sensorReading['lowReading'] ?? null,
                'outOfBounds' => $sensorReading['outOfBounds'],
            ];
        }

        if ($sensorReadingTypeAfterUpdate instanceof TemperatureSensorTypeInterface) {
            $this->checkOutOfBoundResult($readingUpdates, $sensorReadingTypeAfterUpdate->getTemperature(), 'temperature');
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
            'dataPayloadMessage' => [
                [
                    "readingType" => "temperature",
                    "highReading" => 50,
                    "lowReading" => -35,
                    "constRecord" => 0,
                ],
                [
                    "readingType" => "humidity",
                    "highReading" => 95,
                    "lowReading" => 10,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
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
            'dataPayloadMessage' => [
                [
                    "readingType" => "temperature",
                    "highReading" => 75,
                    "lowReading" => 10,
                    "constRecord" => 0,
                ],
                [
                    "readingType" => "humidity",
                    "highReading" => 80,
                    "lowReading" => 5,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
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
            'dataPayloadMessage' => [
                [
                    "readingType" => "temperature",
                    "highReading" => 80,
                    "lowReading" => -40,
                    "constRecord" => 0,
                ],
                [
                    "readingType" => "humidity",
                    "highReading" => 100,
                    "lowReading" => 0,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
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
            'dataPayloadMessage' => [
                [
                    "readingType" => "temperature",
                    "highReading" => 120,
                    "lowReading" => -50,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
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
            'dataPayloadMessage' => [
                [
                    "readingType" => "temperature",
                    "highReading" => 120,
                    "lowReading" => 10,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
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
            'dataPayloadMessage' => [
                [
                    "readingType" => "temperature",
                    "highReading" => 50,
                    "lowReading" => -50,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
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
            'dataPayloadMessage' => [
                [
                    "readingType" => "temperature",
                    "highReading" => 80,
                    "lowReading" => -40,
                    "constRecord" => 0,
                ],
                [
                    "readingType" => "humidity",
                    "highReading" => 95,
                    "lowReading" => 5,
                    "constRecord" => 0,
                ],
                [
                    "readingType" => "latitude",
                    "highReading" => 85,
                    "lowReading" => -85,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
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
            'dataPayloadMessage' => [
                [
                    "readingType" => "temperature",
                    "highReading" => 80,
                    "lowReading" => 10,
                    "constRecord" => 0,
                ],
                [
                    "readingType" => "humidity",
                    "highReading" => 95,
                    "lowReading" => 10,
                    "constRecord" => 0,
                ],
                [
                    "readingType" => "latitude",
                    "highReading" => 85,
                    "lowReading" => -90,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
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
            'dataPayloadMessage' => [
                [
                    "readingType" => "temperature",
                    "highReading" => 50,
                    "lowReading" => -40,
                    "constRecord" => 0,
                ],
                [
                    "readingType" => "humidity",
                    "highReading" => 80,
                    "lowReading" => 5,
                    "constRecord" => 0,
                ],
                [
                    "readingType" => "latitude",
                    "highReading" => 90,
                    "lowReading" => -85,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
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
            'dataPayloadMessage' => [
                [
                    "readingType" => "analog",
                    "highReading" => Soil::HIGH_SOIL_READING_BOUNDARY - 5,
                    "lowReading" => Soil::LOW_SOIL_READING_BOUNDARY + 5,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
        ];

        yield [
            'sensorType' => Soil::class,
            'tableId' => 'soilID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Analog::READING_TYPE,
                    'lowReading' => Soil::LOW_SOIL_READING_BOUNDARY,
                    'outOfBounds' => false,
                ],
            ],
            'dataPayloadMessage' => [
                [
                    "readingType" => "analog",
                    "highReading" => Soil::LOW_SOIL_READING_BOUNDARY,
                    "lowReading" => Soil::LOW_SOIL_READING_BOUNDARY,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
        ];

        yield [
            'sensorType' => Soil::class,
            'tableId' => 'soilID',
            'sensorReadingTypes' => [
                [
                    'readingType' => Analog::READING_TYPE,
                    'highReading' => Soil::HIGH_SOIL_READING_BOUNDARY,
                    'outOfBounds' => false,
                ],
            ],
            'dataPayloadMessage' => [
                [
                    "readingType" => "analog",
                    "highReading" => Soil::HIGH_SOIL_READING_BOUNDARY,
                    "lowReading" => Soil::LOW_SOIL_READING_BOUNDARY,
                    "constRecord" => 0,
                ]
            ],
            'expectedTitle' => UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL,
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
        /** @var AllSensorReadingTypeInterface $sensorTypeObject */
        $sensorTypeObject = $sensorTypeRepository->findAll()[0];
        if ($sensorTypeObject instanceof StandardSensorTypeInterface) {
            $sensorData = [
                'sensorData' => $sensorReadingsToUpdate,
            ];
        }
        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorTypeObject->getSensor()->getSensorID()),
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
        $errorsPayload = $responseData['errors'];

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals($expectedTitle, $title);
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
            $this->checkOutOfBoundResult($readingUpdates, $sensorReadingTypeAfterUpdate->getTemperature(), 'temperature');
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
        /** @var Sensor $sensorObject */
        $sensorObject = $this->entityManager->getRepository(Sensor::class)->findAll()[0];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorObject->getSensorID()),
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
        /** @var AllSensorReadingTypeInterface $sensorTypeObject */
        $sensorTypeObject = $sensorTypeRepository->findAll()[0];

        if ($sensorTypeObject instanceof StandardSensorTypeInterface) {
            $sensorData = [
                $sensorDataToSend
            ];
        }
        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorTypeObject->getSensor()->getSensorID()),
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
            $sensorTypeObject = $sensorRepository->findOneBy(['sensorID' => $wrongSensorId]);

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
        /** @var Sensor $sensorTypeObject */
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
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorTypeObject->getSensorID()),
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
        self::assertEquals('Sensor update builder not found: '. $readingType, $responseData['errors'][0]);
    }

    public function test_sending_request_for_sensor_regular_user_not_apart_of_group(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        /** @var User $loggedInUser */
        $loggedInUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        /** @var User $userNotInGroup */
        $userNotInGroup = $userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $deviceRepository = $this->entityManager->getRepository(Devices::class);
        /** @var Devices $deviceObject */
        $deviceObject = $deviceRepository->findBy(['groupNameID' => $loggedInUser->getGroupNameID()])[0];

        if (in_array($deviceObject->getGroupNameObject()->getGroupNameID(), $userNotInGroup->getAssociatedGroupNameIds(), true)) {
            throw new Exception();
        }

        $sensorRepository = $this->entityManager->getRepository(Sensor::class);
        /** @var Sensor $sensorObjectLoggedInUser */
        $sensorObjectLoggedInUser = $sensorRepository->findBy(['deviceID' => $deviceObject->getDeviceID()])[0];

        $sensorData = [
            'sensorId' => $sensorObjectLoggedInUser->getSensorID(),
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

        $token = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, $sensorObjectLoggedInUser->getSensorID()),
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

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            sprintf(self::UPDATE_SENSOR_BOUNDARY_READING_URL, 1),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_POST],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }
}
