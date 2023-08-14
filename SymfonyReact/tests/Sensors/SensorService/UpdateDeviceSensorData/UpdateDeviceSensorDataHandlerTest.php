<?php

namespace App\Tests\Sensors\SensorService\UpdateDeviceSensorData;

use App\Common\Services\DeviceRequestHandler;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\BusSensorUpdateRequestDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\Factories\SensorUpdateRequestFactory\SensorUpdateRequestBuilderFactory;
use App\Sensors\Repository\Sensors\ORM\SensorTypeRepository;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\UpdateDeviceSensorData\UpdateDeviceSensorDataHandler;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class UpdateDeviceSensorDataHandlerTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    private ContainerInterface|Container $diContainer;

    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeRepository $sensorTypeRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();
        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
        $this->sensorTypeRepository = $this->diContainer->get(SensorTypeRepository::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
      * @dataProvider regularSensorDataProvider
     */
    public function test_preparing_sensor_data_request_dto_regular_sensor(string $sensorType): void
    {
        $sensorTypeObject = $this->sensorTypeRepository->findOneBy(['sensorType' => $sensorType]);
        /** @var Sensor $sensorToUpdate */
        $sensorToUpdate = $this->sensorRepository->findBy(['sensorTypeID' => $sensorTypeObject])[0];

        $sensorUpdateRequestBuilderFactory = $this->diContainer->get(SensorUpdateRequestBuilderFactory::class);
        $deviceRequestHandler = $this->diContainer->get(DeviceRequestHandler::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $sensorUpdateRequestBuilderFactory,
            $deviceRequestHandler,
        );

        $result = $sut->prepareSensorDataRequestDTO($sensorToUpdate);

        self::assertInstanceOf(SingleSensorUpdateRequestDTO::class, $result);
        self::assertEquals($sensorToUpdate->getSensorName(), $result->getSensorName());
        self::assertEquals($sensorToUpdate->getPinNumber(), $result->getPinNumber());
        self::assertEquals($sensorToUpdate->getReadingInterval(), $result->getReadingInterval());
    }

    public function regularSensorDataProvider(): Generator
    {
        yield [
            'sensorType' => Dht::NAME
        ];

        yield [
            'sensorType' => GenericMotion::NAME
        ];

        yield [
            'sensorType' => GenericRelay::NAME
        ];


        yield [
            'sensorType' => Bmp::NAME
        ];
    }

    /**
      * @dataProvider busSensorDataProvider
     */
    public function test_preparing_sensor_data_request_dto_bus_sensor(string $sensorType): void
    {
        $sensorTypeObject = $this->sensorTypeRepository->findOneBy(['sensorType' => $sensorType]);

        /** @var Sensor $sensorToUpdate */
        $sensorToUpdate = $this->sensorRepository->findBy(['sensorTypeID' => $sensorTypeObject])[0];

        $sensorUpdateRequestBuilderFactory = $this->diContainer->get(SensorUpdateRequestBuilderFactory::class);
        $deviceRequestHandler = $this->diContainer->get(DeviceRequestHandler::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $sensorUpdateRequestBuilderFactory,
            $deviceRequestHandler,
        );

        $result = $sut->prepareSensorDataRequestDTO($sensorToUpdate);

        self::assertInstanceOf(BusSensorUpdateRequestDTO::class, $result);

        $allBusSensors = $this->sensorRepository->findAllBusSensors(
            $sensorToUpdate->getDevice()->getDeviceID(),
            $sensorToUpdate->getSensorID(),
            $sensorToUpdate->getSensorID(),
        );

        self::assertEquals($allBusSensors, $result->getSensorNames());
        self::assertEquals($sensorToUpdate->getPinNumber(), $result->getPinNumber());
        self::assertEquals(count($allBusSensors), $result->getSensorCount());
        self::assertEquals($sensorToUpdate->getReadingInterval(), $result->getReadingInterval());
    }

    public function test_sensor_with_unknown_sensor_type_throws_exception(): void
    {
        $newSensorType = new SensorType();
        $newSensorType->setSensorType('unknown');
        $newSensorType->setDescription('unknown');

        $newSensor = new Sensor();
        $newSensor->setSensorName('unknown');
        $newSensor->setSensorTypeID($newSensorType);

        $sensorUpdateRequestBuilderFactory = $this->diContainer->get(SensorUpdateRequestBuilderFactory::class);
        $deviceRequestHandler = $this->diContainer->get(DeviceRequestHandler::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $sensorUpdateRequestBuilderFactory,
            $deviceRequestHandler,
        );

        $this->expectException(SensorTypeNotFoundException::class);
        $sut->prepareSensorDataRequestDTO($newSensor);
    }

    public function busSensorDataProvider(): Generator
    {
        yield [
            'sensorType' => Dallas::NAME
        ];

        yield [
            'sensorType' => Soil::NAME
        ];
    }

    public function test_sending_correct_individual_data_returns_true_individual_sensor(): void
    {
        $sensorUpdateRequestBuilderFactory = $this->diContainer->get(SensorUpdateRequestBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $httpClient = new MockHttpClient($response);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
        );

        $sut = new UpdateDeviceSensorDataHandler(
            $sensorUpdateRequestBuilderFactory,
            $deviceRequestHandler,
        );

        /** @var Sensor $relayRepository */
        $sensorToUpdate = $this->sensorRepository->findAll()[0];

        $sensorUpdateRequestDTO = new SingleSensorUpdateRequestDTO(
            $sensorToUpdate->getSensorName(),
            $sensorToUpdate->getPinNumber(),
            $sensorToUpdate->getReadingInterval(),
        );

        $result = $sut->handleSensorsUpdateRequest($sensorToUpdate, $sensorUpdateRequestDTO);

        self::assertTrue($result);
    }

    public function test_sending_correct_bus_data_returns_true_individual_sensor(): void
    {
        $sensorUpdateRequestBuilderFactory = $this->diContainer->get(SensorUpdateRequestBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $httpClient = new MockHttpClient($response);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
        );

        $sut = new UpdateDeviceSensorDataHandler(
            $sensorUpdateRequestBuilderFactory,
            $deviceRequestHandler,
        );

        /** @var Sensor $relayRepository */
        $sensorToUpdate = $this->sensorRepository->findAll()[0];

        $sensorUpdateRequestDTO = new BusSensorUpdateRequestDTO(
            [$sensorToUpdate->getSensorName()],
            $sensorToUpdate->getPinNumber(),
            1,
            $sensorToUpdate->getReadingInterval(),

        );

        $result = $sut->handleSensorsUpdateRequest($sensorToUpdate, $sensorUpdateRequestDTO);

        self::assertTrue($result);
    }

    public function test_response_not_ok_returns_false(): void
    {
        $sensorUpdateRequestBuilderFactory = $this->diContainer->get(SensorUpdateRequestBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
        );

        $sut = new UpdateDeviceSensorDataHandler(
            $sensorUpdateRequestBuilderFactory,
            $deviceRequestHandler,
        );

        /** @var Sensor $relayRepository */
        $sensorToUpdate = $this->sensorRepository->findAll()[0];

        $sensorUpdateRequestDTO = new BusSensorUpdateRequestDTO(
            [$sensorToUpdate->getSensorName()],
            $sensorToUpdate->getPinNumber(),
            1,
            $sensorToUpdate->getReadingInterval(),

        );

        $result = $sut->handleSensorsUpdateRequest($sensorToUpdate, $sensorUpdateRequestDTO);

        self::assertFalse($result);
    }
}
