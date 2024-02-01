<?php

namespace App\Tests\Sensors\SensorService\SensorReadingUpdate\CurrentReading;

use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingTransportMessageDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\AbstractBoolReadingBaseSensor;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\AbstractStandardReadingType;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\ConstantlyRecord\SensorConstantlyRecordHandlerInterface;
use App\Sensors\SensorServices\OutOfBounds\SensorOutOfBoundsHandlerInterface;
use App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingsHandlerVersionTwo;
use App\Sensors\SensorServices\Trigger\SensorTriggerProcessor\ReadingTriggerHandlerInterface;
use App\Sensors\SensorServices\Trigger\TriggerChecker\SensorReadingTriggerCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Psr\Log\LoggerInterface;
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

    private SensorReadingTriggerCheckerInterface $triggerHandler;

    private SensorRepositoryInterface $sensorRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();
        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->validator = $this->diContainer->get(ValidatorInterface::class);
        $this->sensorReadingTypeRepositoryFactory = $this->diContainer->get(SensorReadingTypeRepositoryFactory::class);
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
        $constRecordService = $this->createMock(SensorConstantlyRecordHandlerInterface::class);
        $outOfBoundsService = $this->createMock(SensorOutOfBoundsHandlerInterface::class);
        $triggerHandler = $this->createMock(ReadingTriggerHandlerInterface::class);

        $outOfBoundsService->expects(self::never())->method('processOutOfBounds');
        $constRecordService->expects(self::never())->method('processConstRecord');
        $triggerHandler->expects(self::never())->method('handleTrigger');

        $logger = $this->createMock(LoggerInterface::class);
        $sut = new UpdateCurrentSensorReadingsHandlerVersionTwo(
            $this->validator,
            $this->sensorReadingTypeRepositoryFactory,
            $outOfBoundsService,
            $constRecordService,
            $triggerHandler,
            $this->sensorRepository,
            $logger,
        );

        /** @var SensorTypeInterface $sensorType */
        $sensorType = $this->entityManager->getRepository($sensorTypeClass)->findAll()[0];

        /** @var Sensor $firstSensor */
        $firstSensor = $this->sensorRepository->findBy(['sensorTypeID' => $sensorType])[0];

        $device = $firstSensor->getDevice();
        $updateSensorCurrentReadingConsumerDTO =  new UpdateSensorCurrentReadingTransportMessageDTO(
            sensorType: $sensorType::getReadingTypeName(),
            sensorName: $firstSensor->getSensorName(),
            currentReadings: array_values($currentReadings),
            deviceID: $device->getDeviceID(),
        );

        $result = $sut->handleUpdateSensorCurrentReading($updateSensorCurrentReadingConsumerDTO);

        self::assertNotEmpty($result);

        self::assertEquals($errorMessages, $result);

        /** @var Sensor $sensorAfterUpdate */
        $sensorAfterUpdate = $this->sensorRepository->find($firstSensor->getSensorID());
        self::assertNotNull($sensorAfterUpdate);

        /** @var AllSensorReadingTypeInterface[] $sensorReadingTypes */
        /** @TODO REPOSITORY FIX */
        $sensorReadingTypes = array_merge(
            $this->entityManager->getRepository(AbstractStandardReadingType::class)->findBySensorID($sensorAfterUpdate->getSensorID()),
            $this->entityManager->getRepository(AbstractBoolReadingBaseSensor::class)->findBySensorID($sensorAfterUpdate->getSensorID()),

        );

        self::assertNotEmpty($sensorReadingTypes);

        foreach ($sensorReadingTypes as $readingType) {
            self::assertNotEquals($readingType->getCurrentReading(), $currentReadings[$readingType::getReadingTypeName()]->getCurrentReading());
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
        $constRecordService = $this->createMock(SensorConstantlyRecordHandlerInterface::class);
        $outOfBoundsService = $this->createMock(SensorOutOfBoundsHandlerInterface::class);
        $triggerHandler = $this->createMock(ReadingTriggerHandlerInterface::class);

        $outOfBoundsService->expects(self::never())->method('processOutOfBounds');
        $constRecordService->expects(self::never())->method('processConstRecord');
        $triggerHandler->expects(self::never())->method('handleTrigger');

        $logger = $this->createMock(LoggerInterface::class);
        $sut = new UpdateCurrentSensorReadingsHandlerVersionTwo(
            $this->validator,
            $this->sensorReadingTypeRepositoryFactory,
            $outOfBoundsService,
            $constRecordService,
            $triggerHandler,
            $this->sensorRepository,
            $logger,
        );

        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findAll();

        $device = $sensors[0]->getDevice();

        $updateSensorCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            sensorType: Dht::NAME,
            sensorName: random_int(10, 32222),
            currentReadings: [
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 32),
                Humidity::READING_TYPE => new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 23)
            ],
            deviceID: $device->getDeviceID(),
        );

        $this->expectException(SensorNotFoundException::class);

        $sut->handleUpdateSensorCurrentReading($updateSensorCurrentReadingMessageDTO);
    }

    public function test_sub_processes_are_not_processed_when_errors_detected(): void
    {
        $outOfBoundsService = $this->createMock(SensorOutOfBoundsHandlerInterface::class);
        $outOfBoundsService->expects(self::never())->method('processOutOfBounds');

        $constRecordService = $this->createMock(SensorConstantlyRecordHandlerInterface::class);
        $constRecordService->expects(self::never())->method('processConstRecord');

        $triggerHandler = $this->createMock(ReadingTriggerHandlerInterface::class);
        $triggerHandler->expects(self::never())->method('handleTrigger');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('error');

        $sut = new UpdateCurrentSensorReadingsHandlerVersionTwo(
            $this->validator,
            $this->sensorReadingTypeRepositoryFactory,
            $outOfBoundsService,
            $constRecordService,
            $triggerHandler,
            $this->sensorRepository,
            $logger,
        );

        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findAll();

        $sensor = $sensors[0];

        $updateSensorCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            sensorType: Dht::NAME,
            sensorName: $sensor->getSensorName(),
            currentReadings: [
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 32),
                Humidity::READING_TYPE => new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING + 23)
            ],
            deviceID: $sensor->getDevice()->getDeviceID(),
        );

        $sut->handleUpdateSensorCurrentReading($updateSensorCurrentReadingMessageDTO);
    }

    /**
     * @dataProvider correctCurrentReadingDataProvider
     */
    public function test_correct_data_is_processed(
        string $sensorType,
        string $sensorTypeClass,
        array $currentReadings,
        ?bool $processOutOfBounds = true,
    ): void {
        $sensorTypeRepository = $this->entityManager->getRepository($sensorTypeClass);

        /** @var AllSensorReadingTypeInterface $sensorReadingType */
        $sensorReadingType = $sensorTypeRepository->findAll()[0];

        /** @var Sensor $firstSensor */
        $firstSensor = $this->sensorRepository->findBy(['sensorTypeID' => $sensorReadingType])[0];

        $device = $firstSensor->getDevice();
        $updateSensorCurrentReadingConsumerDTO =  new UpdateSensorCurrentReadingTransportMessageDTO(
            sensorType: $sensorType,
            sensorName: $firstSensor->getSensorName(),
            currentReadings: array_values($currentReadings),
            deviceID: $device->getDeviceID(),
        );

        $constRecordService = $this->createMock(SensorConstantlyRecordHandlerInterface::class);
        $outOfBoundsService = $this->createMock(SensorOutOfBoundsHandlerInterface::class);
        $triggerHandler = $this->createMock(ReadingTriggerHandlerInterface::class);

        $sensorReadingTypes = array_merge(
            $this->entityManager->getRepository(AbstractStandardReadingType::class)->findBySensorID($firstSensor->getSensorID()),
            $this->entityManager->getRepository(AbstractBoolReadingBaseSensor::class)->findBySensorID($firstSensor->getSensorID()),

        );
        self::assertNotEmpty($sensorReadingTypes);
        $sensorReadingTypesCount = count($sensorReadingTypes);

        if ($processOutOfBounds === true) {
            $outOfBoundsService->expects(self::exactly($sensorReadingTypesCount))->method('processOutOfBounds');
        }
        $constRecordService->expects(self::exactly($sensorReadingTypesCount))->method('processConstRecord');

        $triggerHandler->expects(self::exactly($sensorReadingTypesCount))->method('handleTrigger');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::never())->method('error');

        $sut = new UpdateCurrentSensorReadingsHandlerVersionTwo(
            $this->validator,
            $this->sensorReadingTypeRepositoryFactory,
            $outOfBoundsService,
            $constRecordService,
            $triggerHandler,
            $this->sensorRepository,
            $logger,
        );

        $result = $sut->handleUpdateSensorCurrentReading($updateSensorCurrentReadingConsumerDTO);
        self::assertEmpty($result);

        /** @var Sensor $sensorAfterUpdate */
        $sensorAfterUpdate = $this->sensorRepository->find($firstSensor->getSensorID());
        /** @var AllSensorReadingTypeInterface[] $sensorReadingTypes */
        $sensorReadingTypes = array_merge(
            $this->entityManager->getRepository(AbstractStandardReadingType::class)->findBySensorID($sensorAfterUpdate->getSensorID()),
            $this->entityManager->getRepository(AbstractBoolReadingBaseSensor::class)->findBySensorID($sensorAfterUpdate->getSensorID()),

        );
        self::assertNotEmpty($sensorReadingTypes);

        foreach ($sensorReadingTypes as $readingType) {
            self::assertEquals($readingType->getCurrentReading(), $currentReadings[$readingType::getReadingTypeName()]->getCurrentReading());
        }
    }

    public function correctCurrentReadingDataProvider(): Generator
    {
        yield [
            'sensorType' => Dht::NAME,
            'sensorTypeClass' => Dht::class,
            'currentReadings' => [
                Humidity::READING_TYPE => new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - random_int(1, 20)),
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Dht::HIGH_TEMPERATURE_READING_BOUNDARY - random_int(1, 20))
            ],
        ];

        yield [
            'sensorType' => Dht::NAME,
            'sensorTypeClass' => Dht::class,
            'currentReadings' => [
                Humidity::READING_TYPE => new HumidityCurrentReadingUpdateRequestDTO(Humidity::LOW_READING + random_int(1, 20)),
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Dht::LOW_TEMPERATURE_READING_BOUNDARY + random_int(1, 20))
            ],
        ];

        yield [
            'sensorType' => Dallas::NAME,
            'sensorTypeClass' => Dallas::class,
            'currentReadings' => [
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Dallas::HIGH_TEMPERATURE_READING_BOUNDARY - random_int(1, 20))
            ],
        ];

        yield [
            'sensorType' => Dallas::NAME,
            'sensorTypeClass' => Dallas::class,
            'currentReadings' => [
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Dallas::LOW_TEMPERATURE_READING_BOUNDARY + random_int(1, 20))
            ],
        ];

        yield [
            'sensorType' => Bmp::NAME,
            'sensorTypeClass' => Bmp::class,
            'currentReadings' => [
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Bmp::HIGH_TEMPERATURE_READING_BOUNDARY - random_int(1, 20)),
                Humidity::READING_TYPE => new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - random_int(1, 20)),
                Latitude::READING_TYPE => new LatitudeCurrentReadingUpdateRequestDTO(Latitude::HIGH_READING - random_int(1, 20)),
            ],
        ];

        yield [
            'sensorType' => Bmp::NAME,
            'sensorTypeClass' => Bmp::class,
            'currentReadings' => [
                Temperature::READING_TYPE => new TemperatureCurrentReadingUpdateRequestDTO(Bmp::LOW_TEMPERATURE_READING_BOUNDARY + random_int(1, 20)),
                Humidity::READING_TYPE => new HumidityCurrentReadingUpdateRequestDTO(Humidity::LOW_READING + random_int(1, 20)),
                Latitude::READING_TYPE => new LatitudeCurrentReadingUpdateRequestDTO(Latitude::LOW_READING + random_int(1, 20)),
            ],
        ];

        yield [
            'sensorType' => Soil::NAME,
            'sensorTypeClass' => Soil::class,
            'currentReadings' => [
                Analog::READING_TYPE => new AnalogCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY - random_int(1, 20)),
            ],
        ];

        yield [
            'sensorType' => Soil::NAME,
            'sensorTypeClass' => Soil::class,
            'currentReadings' => [
                Analog::READING_TYPE => new AnalogCurrentReadingUpdateRequestDTO(Soil::LOW_SOIL_READING_BOUNDARY + random_int(1, 20)),
            ],
        ];

        yield [
            'sensorType' => LDR::NAME,
            'sensorTypeClass' => LDR::class,
            'currentReadings' => [
                Analog::READING_TYPE => new AnalogCurrentReadingUpdateRequestDTO(LDR::HIGH_READING - random_int(1, 20)),
            ],
        ];

        yield [
            'sensorType' => LDR::NAME,
            'sensorTypeClass' => LDR::class,
            'currentReadings' => [
                Analog::READING_TYPE => new AnalogCurrentReadingUpdateRequestDTO(LDR::LOW_READING + random_int(1, 20)),
            ],
        ];

        yield [
            'sensorType' => GenericMotion::NAME,
            'sensorTypeClass' => GenericMotion::class,
            'currentReadings' => [
                Motion::READING_TYPE => new BoolCurrentReadingUpdateDTO(Motion::getReadingTypeName(), true),
            ],
            'outOfBounds' => false,
        ];

        yield [
            'sensorType' => GenericMotion::NAME,
            'sensorTypeClass' => GenericMotion::class,
            'currentReadings' => [
                Motion::READING_TYPE => new BoolCurrentReadingUpdateDTO(Motion::getReadingTypeName(), false),
            ],
            'outOfBounds' => false,
        ];

        yield [
            'sensorType' => GenericRelay::NAME,
            'sensorTypeClass' => GenericRelay::class,
            'currentReadings' => [
                Relay::READING_TYPE => new BoolCurrentReadingUpdateDTO(Relay::getReadingTypeName(), true),
            ],
            'outOfBounds' => false,
        ];

        yield [
            'sensorType' => GenericRelay::NAME,
            'sensorTypeClass' => GenericRelay::class,
            'currentReadings' => [
                Relay::READING_TYPE => new BoolCurrentReadingUpdateDTO(Relay::getReadingTypeName(), false),
            ],
            'outOfBounds' => false,
        ];
    }
}
