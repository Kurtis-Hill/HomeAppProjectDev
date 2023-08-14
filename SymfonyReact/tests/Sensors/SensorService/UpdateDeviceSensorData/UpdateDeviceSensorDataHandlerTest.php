<?php

namespace App\Tests\Sensors\SensorService\UpdateDeviceSensorData;

use App\Common\Services\DeviceRequestHandler;
use App\Devices\Builders\Request\DeviceSettingsRequestDTOBuilder;
use App\Sensors\Builders\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\ORM\SensorTypeRepository;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\UpdateDeviceSensorData\UpdateDeviceSensorDataHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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

    public function test_no_sensors_found_throws_sensor_not_found_exception(): void
    {
        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
        $singleSensorUpdateRequestDTOBuilder = $this->diContainer->get(SingleSensorUpdateRequestDTOBuilder::class);

        $deviceRequestHandler = $this->diContainer->get(DeviceRequestHandler::class);

        $mockLogger = $this->createMock(LoggerInterface::class);

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
            $sensorTypeRepositoryFactory,
            $singleSensorUpdateRequestDTOBuilder,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );

        $this->expectException(SensorNotFoundException::class);

        while (true) {
            $randomSensorID = random_int(1, 9999);
            $sensor = $this->sensorRepository->findOneBy(['sensorID' => $randomSensorID]);
            if ($sensor === null) {
                break;
            }
        }
        $sut->handleSensorsUpdateRequest([$randomSensorID]);
    }

    public function test_sensor_type_doesnt_exist_logs_error(): void
    {
        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
        $singleSensorUpdateRequestDTOBuilder = $this->diContainer->get(SingleSensorUpdateRequestDTOBuilder::class);

        $deviceRequestHandler = $this->diContainer->get(DeviceRequestHandler::class);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::once())->method('error');

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
            $sensorTypeRepositoryFactory,
            $singleSensorUpdateRequestDTOBuilder,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );

        $mockSensorType = $this->createMock(SensorType::class);
        $mockSensorType->method('getSensorType')->willReturn('unknown');

        $sensor = $this->sensorRepository->findAll()[0];
        $sensor->setSensorTypeID($mockSensorType);

        $this->expectException(SensorNotFoundException::class);
        $sut->handleSensorsUpdateRequest([$sensor->getSensorID()]);
    }





    public function test_response_not_ok_returns_false(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
        $singleSensorUpdateRequestDTOBuilder = $this->diContainer->get(SingleSensorUpdateRequestDTOBuilder::class);
        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
            $sensorTypeRepositoryFactory,
            $singleSensorUpdateRequestDTOBuilder,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );

        /** @var Sensor $relayRepository */
        $sensorToUpdate = $this->sensorRepository->findAll()[0];

        $result = $sut->handleSensorsUpdateRequest([$sensorToUpdate->getSensorID()]);

        self::assertFalse($result);
    }

    public function test_unsuccessful_request_returns_false(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
        $singleSensorUpdateRequestDTOBuilder = $this->diContainer->get(SingleSensorUpdateRequestDTOBuilder::class);
        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
            $sensorTypeRepositoryFactory,
            $singleSensorUpdateRequestDTOBuilder,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );

        /** @var Sensor $relayRepository */
        $sensorToUpdate = $this->sensorRepository->findAll()[0];

        $result = $sut->handleSensorsUpdateRequest([$sensorToUpdate->getSensorID()]);

        self::assertFalse($result);
    }
}
