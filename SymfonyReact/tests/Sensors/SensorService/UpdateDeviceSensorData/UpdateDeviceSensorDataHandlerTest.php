<?php

namespace App\Tests\Sensors\SensorService\UpdateDeviceSensorData;

use App\Common\Services\DeviceRequestHandler;
use App\Devices\Builders\Request\DeviceSettingsRequestDTOBuilder;
use App\Sensors\Builders\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\AbstractSensorType;
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

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();
        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_no_sensors_found_throws_sensor_not_found_exception(): void
    {
        $deviceRequestHandler = $this->diContainer->get(DeviceRequestHandler::class);

        $mockLogger = $this->createMock(LoggerInterface::class);

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );

        $this->expectException(SensorNotFoundException::class);

        while (true) {
            $randomSensorID = random_int(1, 9999);
            $sensor = $this->sensorRepository->findOneBy(['sensorID' => $randomSensorID]);
            if ($sensor === null) {
                $sensorUpdateRequestDTO = new SingleSensorUpdateRequestDTO(
                    'test',
                    1,
                    1
                );
                break;
            }
        }
        $sut->handleSensorsUpdateRequest([$sensorUpdateRequestDTO]);
    }

    public function test_sensor_type_doesnt_exist_logs_error(): void
    {
        $deviceRequestHandler = $this->diContainer->get(DeviceRequestHandler::class);

        $mockLogger = $this->createMock(LoggerInterface::class);

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );

        $this->expectException(SensorNotFoundException::class);

        $singleSensorUpdateRequestDTO = new SingleSensorUpdateRequestDTO(
            'test',
            1,
            1
        );
        $sut->handleSensorsUpdateRequest([$singleSensorUpdateRequestDTO]);
    }

    public function test_response_not_ok_returns_false(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $mockLogger = $this->createMock(LoggerInterface::class);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );

        /** @var Sensor $relayRepository */
        $sensorToUpdate = $this->sensorRepository->findAll()[0];

        $sensorUpdate = new SingleSensorUpdateRequestDTO(
            $sensorToUpdate->getSensorName(),
            $sensorToUpdate->getPinNumber(),
            $sensorToUpdate->getReadingInterval()
        );
        $result = $sut->handleSensorsUpdateRequest([$sensorUpdate]);

        self::assertFalse($result);
    }

    public function test_unsuccessful_request_returns_false(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $mockLogger = $this->createMock(LoggerInterface::class);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $sut = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );

        /** @var Sensor $relayRepository */
        $sensorToUpdate = $this->sensorRepository->findAll()[0];

        $sensorUpdate = new SingleSensorUpdateRequestDTO(
            $sensorToUpdate->getSensorName(),
            $sensorToUpdate->getPinNumber(),
            $sensorToUpdate->getReadingInterval()
        );

        $result = $sut->handleSensorsUpdateRequest([$sensorUpdate]);

        self::assertFalse($result);
    }
}
