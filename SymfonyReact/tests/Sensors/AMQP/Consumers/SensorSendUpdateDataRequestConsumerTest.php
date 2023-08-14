<?php

namespace App\Tests\Sensors\AMQP\Consumers;

use App\Common\Services\DeviceRequestHandler;
use App\Devices\Builders\Request\DeviceSettingsRequestDTOBuilder;
use App\Devices\DeviceServices\Request\DeviceSettingsUpdateRequestHandler;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ORM\DataFixtures\ESP8266\SensorFixtures;
use App\Sensors\AMQP\Consumers\SensorSendUpdateDataRequestConsumer;
use App\Sensors\Builders\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
use App\Sensors\DTO\Internal\Event\SensorUpdateEventDTO;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Factories\SensorUpdateRequestFactory\SensorUpdateRequestBuilderFactory;
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

    private DeviceRepositoryInterface $deviceRepository;

    private SensorRepositoryInterface $sensorRepository;

    private ContainerAwareInterface|Container $diContainer;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();

        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->deviceRepository = $this->diContainer->get(DeviceRepositoryInterface::class);
        $this->sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
    }

    public function test_successful_request_returns_true(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $httpClient = new MockHttpClient($response);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
        );

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $deviceSettingsUpdateRequestHandler = new DeviceSettingsUpdateRequestHandler(
            $deviceRequestHandler,
            $this->deviceRepository,
            $deviceSettingsRequestDTOBuilder,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::once())->method('info');
        $mockLogger->expects(self::never())->method('error');

        $sensorUpdateRequestBuilderFactory = $this->diContainer->get(SensorUpdateRequestBuilderFactory::class);
        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
        $singleSensorUpdateRequestDTOBuilder = $this->diContainer->get(SingleSensorUpdateRequestDTOBuilder::class);

        $updateDeviceSensorDataHandler = new UpdateDeviceSensorDataHandler(
            $sensorUpdateRequestBuilderFactory,
            $deviceRequestHandler,
            $this->sensorRepository,
            $sensorTypeRepositoryFactory,
            $singleSensorUpdateRequestDTOBuilder,
            $mockLogger,
        );
        $this->sut = new SensorSendUpdateDataRequestConsumer(
            $updateDeviceSensorDataHandler,
            $mockLogger
        );

        $sensorToUpdate = $this->sensorRepository->findOneBy(['sensorName' => SensorFixtures::RELAY_SENSOR_NAME]);
        $sensorUpdateEventDTO = new SensorUpdateEventDTO(
            [$sensorToUpdate->getSensorID()]
        );
        $amqpMess = new AMQPMessage(serialize($sensorUpdateEventDTO));

        $result = $this->sut->execute($amqpMess);

        self::assertTrue($result);
    }

    public function test_unsuccessful_request_returns_false(): void
    {
        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
        );

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $deviceSettingsUpdateRequestHandler = new DeviceSettingsUpdateRequestHandler(
            $deviceRequestHandler,
            $this->deviceRepository,
            $deviceSettingsRequestDTOBuilder,
        );

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::once())->method('error');
        $mockLogger->expects(self::never())->method('info');

        $sensorUpdateRequestBuilderFactory = $this->diContainer->get(SensorUpdateRequestBuilderFactory::class);
        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
        $singleSensorUpdateRequestDTOBuilder = $this->diContainer->get(SingleSensorUpdateRequestDTOBuilder::class);

        $updateDeviceSensorDataHandler = new UpdateDeviceSensorDataHandler(
            $sensorUpdateRequestBuilderFactory,
            $deviceRequestHandler,
            $this->sensorRepository,
            $sensorTypeRepositoryFactory,
            $singleSensorUpdateRequestDTOBuilder,
            $mockLogger,
        );
        $this->sut = new SensorSendUpdateDataRequestConsumer(
            $updateDeviceSensorDataHandler,
            $mockLogger
        );

        $sensorToUpdate = $this->sensorRepository->findOneBy(['sensorName' => SensorFixtures::RELAY_SENSOR_NAME]);
        $sensorUpdateEventDTO = new SensorUpdateEventDTO(
            [$sensorToUpdate->getSensorID()]
        );
        $amqpMess = new AMQPMessage(serialize($sensorUpdateEventDTO));

        $result = $this->sut->execute($amqpMess);

        self::assertFalse($result);
    }
}
