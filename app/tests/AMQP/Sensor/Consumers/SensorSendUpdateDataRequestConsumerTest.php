<?php

namespace AMQP\Sensor\Consumers;

use App\AMQP\Sensor\Consumers\SensorSendUpdateDataRequestConsumer;
use App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder;
use App\Builders\Sensor\Internal\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
use App\DataFixtures\ESP8266\SensorFixtures;
use App\DTOs\Sensor\Internal\Event\SensorUpdateEventDTO;
use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Entity\Sensor\Sensor;
use App\Factories\Sensor\SensorType\SensorTypeRepositoryFactory;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Device\Request\DeviceRequestHandler;
use App\Services\Sensor\UpdateDeviceSensorData\UpdateDeviceSensorDataHandler;
use Doctrine\ORM\EntityManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class SensorSendUpdateDataRequestConsumerTest extends KernelTestCase
{
    private SensorSendUpdateDataRequestConsumer $sut;

    private ?EntityManagerInterface $entityManager;

    private SensorRepositoryInterface $sensorRepository;

    private ContainerAwareInterface|Container $diContainer;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();

        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
    }

    public function test_successful_request_returns_true(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $httpClient = new MockHttpClient($response);

        $deviceMockLogger = $this->createMock(LoggerInterface::class);
        $deviceMockLogger->expects(self::once())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $deviceMockLogger,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::once())->method('info');
        $mockLogger->expects(self::never())->method('error');

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $updateDeviceSensorDataHandler = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
//            $sensorTypeRepositoryFactory,
//            $singleSensorUpdateRequestDTOBuilder,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );
        $this->sut = new SensorSendUpdateDataRequestConsumer(
            $updateDeviceSensorDataHandler,
            $mockLogger
        );

        $sensorToUpdate = $this->sensorRepository->findOneBy(['sensorName' => SensorFixtures::ADMIN_1_RELAY_SENSOR_NAME]);

        /** @var \App\Builders\Sensor\Internal\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder $singleSensorUpdateRequestDTOBuilder */
        $singleSensorUpdateRequestDTOBuilder = $this->diContainer->get(SingleSensorUpdateRequestDTOBuilder::class);

        $sensorRequestDTOs = $singleSensorUpdateRequestDTOBuilder->buildSensorUpdateRequestDTO($sensorToUpdate);

        $sensorUpdateEventDTO = new SensorUpdateEventDTO(
            [$sensorRequestDTOs]
        );
        $amqpMess = new AMQPMessage(serialize($sensorUpdateEventDTO));

        $result = $this->sut->execute($amqpMess);

        self::assertTrue($result);
    }

    public function test_unsuccessful_request_returns_false(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $deviceMockLogger = $this->createMock(LoggerInterface::class);
//        $deviceMockLogger->expects(self::once())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $deviceMockLogger,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::once())->method('error');
        $mockLogger->expects(self::never())->method('info');

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $updateDeviceSensorDataHandler = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
//            $sensorTypeRepositoryFactory,
//            $singleSensorUpdateRequestDTOBuilder,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );
        $this->sut = new SensorSendUpdateDataRequestConsumer(
            $updateDeviceSensorDataHandler,
            $mockLogger
        );

        $sensorToUpdate = $this->sensorRepository->findOneBy(['sensorName' => SensorFixtures::ADMIN_1_RELAY_SENSOR_NAME]);
        $singleSensorUpdateRequestDTOBuilder = $this->diContainer->get(SingleSensorUpdateRequestDTOBuilder::class);
        $sensorRequestDTOs = $singleSensorUpdateRequestDTOBuilder->buildSensorUpdateRequestDTO($sensorToUpdate);

        $sensorUpdateEventDTO = new SensorUpdateEventDTO(
            [$sensorRequestDTOs]
        );
        $amqpMess = new AMQPMessage(serialize($sensorUpdateEventDTO));

        $result = $this->sut->execute($amqpMess);

        self::assertFalse($result);
    }

    public function test_sending_sensor_id_that_doesnt_exist_returns_true(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $httpClient = new MockHttpClient($response);

        $deviceMockLogger = $this->createMock(LoggerInterface::class);
        $deviceMockLogger->expects(self::never())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $deviceMockLogger,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::never())->method('info');
        $mockLogger->expects(self::once())->method('error');

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
        $singleSensorUpdateRequestDTOBuilder = $this->diContainer->get(SingleSensorUpdateRequestDTOBuilder::class);
        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $updateDeviceSensorDataHandler = new UpdateDeviceSensorDataHandler(
            $deviceRequestHandler,
            $this->sensorRepository,
//            $sensorTypeRepositoryFactory,
//            $singleSensorUpdateRequestDTOBuilder,
            $deviceSettingsRequestDTOBuilder,
            $mockLogger,
        );
        $this->sut = new SensorSendUpdateDataRequestConsumer(
            $updateDeviceSensorDataHandler,
            $mockLogger
        );

        $mockSensor = new Sensor();
        $mockSensor->setSensorName('NAANONAME');
        $mockSensor->setPinNumber(2);

        $sensorRequestDTO = new SingleSensorUpdateRequestDTO(
            $mockSensor->getSensorName(),
            $mockSensor->getPinNumber(),
            $mockSensor->getReadingInterval()
        );

        $sensorUpdateEventDTO = new SensorUpdateEventDTO(
            [$sensorRequestDTO],
        );

        $amqpMess = new AMQPMessage(serialize($sensorUpdateEventDTO));

        $result = $this->sut->execute($amqpMess);

        self::assertTrue($result);
    }
}
