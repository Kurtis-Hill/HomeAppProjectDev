<?php

namespace App\Tests\Sensors\SensorService\SensorReadingUpdate\CurrentReading;

use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\ConstantlyRecord\SensorConstantlyRecordHandlerInterface;
use App\Sensors\SensorServices\OutOfBounds\SensorOutOfBoundsHandlerInterface;
use App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingsHandlerVersionTwo;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateCurrentSensorReadingsHandlerVersionTwoTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    private ContainerInterface|Container $diContainer;

    private ValidatorInterface $validator;

    private SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory;

    private SensorOutOfBoundsHandlerInterface $outOfBoundsSensorService;

    private SensorConstantlyRecordHandlerInterface $constantlyRecordService;

    private SensorRepositoryInterface $sensorRepository;


    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();
        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->validator = $this->diContainer->get(ValidatorInterface::class);
        $this->sensorReadingTypeRepositoryFactory = $this->diContainer->get(SensorReadingTypeRepositoryFactory::class);
        $this->outOfBoundsSensorService = $this->diContainer->get(SensorOutOfBoundsHandlerInterface::class);
        $this->constantlyRecordService = $this->diContainer->get(SensorConstantlyRecordHandlerInterface::class);
        $this->sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider wrongCurrentReadingDataProvider
     */
    public function test_update_current_reading_consumer_dto_validation_wrong_data(
        string $sensorType,
        string $sensorTypeClass,
        array $currentReadings,
        array $errorMessages,
    ): void {
        $sut = new UpdateCurrentSensorReadingsHandlerVersionTwo(
            $this->validator,
            $this->sensorReadingTypeRepositoryFactory,
            $this->outOfBoundsSensorService,
            $this->constantlyRecordService,
            $this->sensorRepository,
        );

        $sensorTypeRepository = $this->entityManager->getRepository($sensorTypeClass);

        /** @var SensorTypeInterface[] $allSensors */
        $allSensors = $sensorTypeRepository->findAll();
        $firstSensor = $allSensors[0];

        $device = $firstSensor->getSensor()->getDevice();
        $updateSensorCurrentReadingConsumerDTO =  new UpdateSensorCurrentReadingMessageDTO(
            sensorType: $sensorType,
            sensorName: $firstSensor->getSensor()->getSensorName(),
            currentReadings: array_values($currentReadings),
            deviceID: $device->getDeviceID(),
        );

        $result = $sut->handleUpdateSensorCurrentReading(
            $updateSensorCurrentReadingConsumerDTO,
            $device,
        );

        self::assertNotEmpty($result);

        self::assertEquals($errorMessages, $result);

        /** @var SensorTypeInterface $sensorAfterUpdate */
        $sensorAfterUpdate = $sensorTypeRepository->find($firstSensor->getSensorTypeID());
        self::assertNotNull($sensorAfterUpdate);

        foreach ($sensorAfterUpdate->getReadingTypes() as $readingType) {
            self::assertNotEquals($readingType->getCurrentReading(), $currentReadings[$readingType->getReadingTypeName()]->getCurrentReading());
        }
    }

    public function wrongCurrentReadingDataProvider(): Generator
    {
        yield [
            'sensorType' => Dht::NAME,
            'sensorTypeClass' => Dht::class,
            'currentReadings' => [
                Temperature::READING_TYPE => new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING + 23),
                Humidity::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 32)
            ],
            'errorMessages' => [
                'Temperature settings for Dht sensor cannot exceed ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY . '°C you entered ' . Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 32 .'°C',
                'Humidity for this sensor cannot be over ' . Humidity::HIGH_READING . '% you entered ' . Humidity::HIGH_READING + 23 . '%'
            ]
        ];

        yield [
            'sensorType' => Dht::NAME,
            'sensorTypeClass' => Dht::class,
            'currentReadings' => [
                Humidity::READING_TYPE =>new HumidityCurrentReadingUpdateRequestDTO(Humidity::LOW_READING - 23),
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Dht::LOW_TEMPERATURE_READING_BOUNDARY - 32)
            ],
            'errorMessages' => [
                'Temperature settings for Dht sensor cannot be below ' . Dht::LOW_TEMPERATURE_READING_BOUNDARY . '°C you entered ' . Dht::LOW_TEMPERATURE_READING_BOUNDARY - 32 .'°C',
                'Humidity for this sensor cannot be under ' . Humidity::LOW_READING . '% you entered ' . Humidity::LOW_READING - 23 . '%'
            ]
        ];

        yield [
            'sensorType' => Dallas::NAME,
            'sensorTypeClass' => Dallas::class,
            'currentReadings' => [
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Dallas::HIGH_TEMPERATURE_READING_BOUNDARY + 3)
            ],
            'errorMessages' => [
                'Temperature settings for Dallas sensor cannot exceed ' . Dallas::HIGH_TEMPERATURE_READING_BOUNDARY . '°C you entered ' . Dallas::HIGH_TEMPERATURE_READING_BOUNDARY + 3 .'°C'
            ]
        ];

        yield [
            'sensorType' => Dallas::NAME,
            'sensorTypeClass' => Dallas::class,
            'currentReadings' => [
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Dallas::LOW_TEMPERATURE_READING_BOUNDARY - 3)
            ],
            'errorMessages' => [
                'Temperature settings for Dallas sensor cannot be below ' . Dallas::LOW_TEMPERATURE_READING_BOUNDARY . '°C you entered ' . Dallas::LOW_TEMPERATURE_READING_BOUNDARY - 3 .'°C'
            ]
        ];

        yield [
            'sensorType' => Bmp::NAME,
            'sensorTypeClass' => Bmp::class,
            'currentReadings' => [
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Bmp::HIGH_TEMPERATURE_READING_BOUNDARY + 3),
                Humidity::READING_TYPE => new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING + 23),
                Latitude::READING_TYPE => new LatitudeCurrentReadingUpdateRequestDTO(Latitude::HIGH_READING + 23),
            ],
            'errorMessages' => [
                'The highest possible latitude is ' . Latitude::HIGH_READING . '° you entered ' . Latitude::HIGH_READING + 23 . '°',
                'Humidity for this sensor cannot be over ' . Humidity::HIGH_READING . '% you entered ' . Humidity::HIGH_READING + 23 . '%',
                'Temperature settings for Bmp sensor cannot exceed ' . Bmp::HIGH_TEMPERATURE_READING_BOUNDARY . '°C you entered ' . Bmp::HIGH_TEMPERATURE_READING_BOUNDARY + 3 .'°C',
            ]
        ];

        yield [
            'sensorType' => Bmp::NAME,
            'sensorTypeClass' => Bmp::class,
            'currentReadings' => [
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Bmp::LOW_TEMPERATURE_READING_BOUNDARY - 3),
                Humidity::READING_TYPE => new HumidityCurrentReadingUpdateRequestDTO(Humidity::LOW_READING - 23),
                Latitude::READING_TYPE => new LatitudeCurrentReadingUpdateRequestDTO(Latitude::LOW_READING - 23),
            ],
            'errorMessages' => [
                'The lowest possible latitude is ' . Latitude::LOW_READING . '° you entered ' . Latitude::LOW_READING - 23 . '°',
                'Humidity for this sensor cannot be under ' . Humidity::LOW_READING . '% you entered ' . Humidity::LOW_READING - 23 . '%',
                'Temperature settings for Bmp sensor cannot be below ' . Bmp::LOW_TEMPERATURE_READING_BOUNDARY . '°C you entered ' . Bmp::LOW_TEMPERATURE_READING_BOUNDARY - 3 .'°C',
            ]
        ];

        yield [
            'sensorType' => Soil::NAME,
            'sensorTypeClass' => Soil::class,
            'currentReadings' => [
                Analog::READING_TYPE =>new AnalogCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY + 3),
            ],
            'errorMessages' => [
                'Reading for Soil sensor cannot be over ' . Soil::HIGH_SOIL_READING_BOUNDARY . ' you entered ' . Soil::HIGH_SOIL_READING_BOUNDARY + 3,
            ]
        ];

        yield [
            'sensorType' => Soil::NAME,
            'sensorTypeClass' => Soil::class,
            'currentReadings' => [
                Analog::READING_TYPE =>new AnalogCurrentReadingUpdateRequestDTO(Soil::LOW_SOIL_READING_BOUNDARY - 3),
            ],
            'errorMessages' => [
                'Reading for Soil sensor cannot be under ' . Soil::LOW_SOIL_READING_BOUNDARY . ' you entered ' . Soil::LOW_SOIL_READING_BOUNDARY - 3,
            ]
        ];

        yield [
            'sensorType' => LDR::NAME,
            'sensorTypeClass' => LDR::class,
            'currentReadings' => [
                Analog::READING_TYPE =>new AnalogCurrentReadingUpdateRequestDTO(LDR::HIGH_READING + 3),
            ],
            'errorMessages' => [
                'Reading for Ldr sensor cannot be over ' . LDR::HIGH_READING . ' you entered ' . LDR::HIGH_READING + 3,
            ]
        ];

        yield [
            'sensorType' => LDR::NAME,
            'sensorTypeClass' => LDR::class,
            'currentReadings' => [
                Analog::READING_TYPE =>new AnalogCurrentReadingUpdateRequestDTO(LDR::LOW_READING - 3),
            ],
            'errorMessages' => [
                'Reading for Ldr sensor cannot be under ' . LDR::LOW_READING . ' you entered ' . LDR::LOW_READING - 3,
            ]
        ];
    }

    public function test_sensor_name_doesnt_exist_throws_sensor_not_found_exception(): void
    {

    }

    public function test_const_record_isnt_processed_when_errors_detected(): void
    {

    }

    public function test_out_of_bounds_isnt_processed_when_errors_detected(): void
    {

    }

    public function test_correct_data_is_processed(): void
    {

    }
}
