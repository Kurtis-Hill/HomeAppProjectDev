<?php

namespace App\Tests\Sensors\AMQP\Consumers;

use App\Common\Services\DeviceRequestHandler;
use App\Devices\Builders\Request\DeviceSettingsRequestDTOBuilder;
use App\ORM\DataFixtures\ESP8266\SensorFixtures;
use App\Sensors\AMQP\Consumers\SensorSendUpdateDataRequestConsumer;
use App\Sensors\Builders\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
use App\Sensors\DTO\Internal\Event\SensorUpdateEventDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Sensors\DTO\Request\SensorUpdateRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\UpdateDeviceSensorData\UpdateDeviceSensorDataHandler;
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

        $sensorToUpdate = $this->sensorRepository->findOneBy(['sensorName' => SensorFixtures::RELAY_SENSOR_NAME]);

        /** @var SingleSensorUpdateRequestDTOBuilder $singleSensorUpdateRequestDTOBuilder */
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

        $sensorToUpdate = $this->sensorRepository->findOneBy(['sensorName' => SensorFixtures::RELAY_SENSOR_NAME]);
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
