<?php

namespace AMQP\Sensor\Consumers;

use App\AMQP\Sensor\Consumers\SensorSendUpdateDataRequestConsumer;
use App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder;
use App\Builders\Sensor\Internal\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
use App\DataFixtures\ESP8266\SensorFixtures;
use App\DTOs\Sensor\Internal\Event\SensorUpdateEventDTO;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Device\Request\DeviceRequestHandler;
use App\Services\Sensor\UpdateDeviceSensorData\UpdateDeviceSensorDataHandler;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class SensorSendUpdateDataRequestConsumerTest extends KernelTestCase
{
    private SensorSendUpdateDataRequestConsumer $sut;

    private ?EntityManagerInterface $entityManager;

    private SensorRepositoryInterface $sensorRepository;

    private Container $diContainer;

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
        $deviceMockLogger->expects($this->once())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $deviceMockLogger,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects($this->once())->method('info');
        $mockLogger->expects($this->never())->method('error');

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $updateDeviceSensorDataHandler = new UpdateDeviceSensorDataHandler(
            deviceRequestHandler: $deviceRequestHandler,
            sensorRepository: $this->sensorRepository,
            deviceSettingsRequestDTOBuilder: $deviceSettingsRequestDTOBuilder,
            logger: $mockLogger,
        );
        $this->sut = new SensorSendUpdateDataRequestConsumer(
            $updateDeviceSensorDataHandler,
            $this->sensorRepository,
            new SingleSensorUpdateRequestDTOBuilder(),
            $mockLogger,
        );

        $sensorToUpdate = $this->sensorRepository->findOneBy(['sensorName' => SensorFixtures::ADMIN_1_RELAY_SENSOR_NAME]);

        $sensorUpdateEventDTO = new SensorUpdateEventDTO(
            $sensorToUpdate->getSensorID()
        );
        $amqpMess = new AMQPMessage(serialize($sensorUpdateEventDTO));

        $result = $this->sut->execute($amqpMess);

        self::assertEquals(ConsumerInterface::MSG_ACK, $result);
    }

    public function test_unsuccessful_request_returns_false(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $deviceMockLogger = $this->createMock(LoggerInterface::class);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $deviceMockLogger,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects($this->once())->method('error');
        $mockLogger->expects($this->never())->method('info');

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $updateDeviceSensorDataHandler = new UpdateDeviceSensorDataHandler(
            deviceRequestHandler: $deviceRequestHandler,
            sensorRepository: $this->sensorRepository,
            deviceSettingsRequestDTOBuilder: $deviceSettingsRequestDTOBuilder,
            logger: $mockLogger,
        );
        $this->sut = new SensorSendUpdateDataRequestConsumer(
            $updateDeviceSensorDataHandler,
            $this->sensorRepository,
            new SingleSensorUpdateRequestDTOBuilder(),
            $mockLogger
        );

        $sensorToUpdate = $this->sensorRepository->findOneBy(['sensorName' => SensorFixtures::ADMIN_1_RELAY_SENSOR_NAME]);

        $sensorUpdateEventDTO = new SensorUpdateEventDTO(
            $sensorToUpdate->getSensorID()
        );
        $amqpMess = new AMQPMessage(serialize($sensorUpdateEventDTO));

        $result = $this->sut->execute($amqpMess);

        self::assertEquals(ConsumerInterface::MSG_REJECT, $result);
    }

    public function test_sending_sensor_id_that_doesnt_exist_returns_true(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $httpClient = new MockHttpClient($response);

        $deviceMockLogger = $this->createMock(LoggerInterface::class);
        $deviceMockLogger->expects($this->never())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $deviceMockLogger,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects($this->never())->method('info');
        $mockLogger->expects($this->any())->method('error');

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $updateDeviceSensorDataHandler = new UpdateDeviceSensorDataHandler(
            deviceRequestHandler: $deviceRequestHandler,
            sensorRepository: $this->sensorRepository,
            deviceSettingsRequestDTOBuilder: $deviceSettingsRequestDTOBuilder,
            logger: $mockLogger,
        );
        $this->sut = new SensorSendUpdateDataRequestConsumer(
            $updateDeviceSensorDataHandler,
            $this->sensorRepository,
            new SingleSensorUpdateRequestDTOBuilder(),
            $mockLogger
        );

        $sensorUpdateEventDTO = new SensorUpdateEventDTO(
            999,
        );

        $amqpMess = new AMQPMessage(serialize($sensorUpdateEventDTO));

        $result = $this->sut->execute($amqpMess);

        self::assertEquals(ConsumerInterface::MSG_ACK, $result);
    }
}
