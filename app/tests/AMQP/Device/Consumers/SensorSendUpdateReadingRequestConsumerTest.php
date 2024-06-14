<?php

namespace AMQP\Device\Consumers;

use App\AMQP\Device\Consumers\SensorSendUpdateReadingRequestConsumer;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateTransportMessageDTO;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Factories\Device\DeviceSensorRequestArgumentBuilderFactory;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Repository\Sensor\ReadingType\ORM\MotionRepository;
use App\Repository\Sensor\ReadingType\ORM\RelayRepository;
use App\Repository\Sensor\Sensors\ORM\SensorRepository;
use App\Repository\Sensor\Sensors\ORM\SensorTypeRepository;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Repository\Sensor\SensorType\ORM\GenericRelayRepository;
use App\Services\Device\Request\DeviceRequestHandler;
use App\Services\Sensor\SensorReadingUpdate\RequestReading\SensorUpdateCurrentReadingRequestHandler;
use Doctrine\ORM\EntityManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class SensorSendUpdateReadingRequestConsumerTest extends KernelTestCase
{
    private SensorSendUpdateReadingRequestConsumer $sut;

    private ?EntityManagerInterface $entityManager;

    private ?RelayRepository $relayRepository;

    private ContainerInterface|Container $diContainer;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();
        $this->sut = $this->diContainer->get(\App\AMQP\Device\Consumers\SensorSendUpdateReadingRequestConsumer  ::class);
        $this->relayRepository = $this->diContainer->get(RelayRepository::class);

        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_successful_http_request_returns_true(): void
    {
        $boolCurrentReadingUpdateDTO = new BoolCurrentReadingUpdateDTO(
            GenericRelay::NAME,
            1,
        );
        /** @var SensorTypeRepository $sensorTypeRepository */
        $sensorTypeRepository = $this->diContainer->get(GenericRelayRepository::class);
        /** @var \App\Entity\Sensor\SensorTypes\GenericRelay $genericSensorType */
        $genericSensorType = $sensorTypeRepository->findAll()[0];
        /** @var SensorRepository $sensorRepository */
        $sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
        $sensorID = $sensorRepository->findBy(['sensorTypeID' => $genericSensorType])[0]->getSensorID();
        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateTransportMessageDTO(
            $sensorID,
            $boolCurrentReadingUpdateDTO,
        );

        $amqpMess = new AMQPMessage(serialize($requestSensorCurrentReadingUpdateMessageDTO));

        $sensorReadingTypeRepositoryFactory = $this->diContainer->get(SensorReadingTypeRepositoryFactory::class);
        $deviceSensorRequestArgumentBuilderFactory = $this->diContainer->get(DeviceSensorRequestArgumentBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $httpClient = new MockHttpClient($response);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::never())->method('error');
        $mockLogger->expects(self::once())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );
        $sensorUpdateCurrentReadingRequestHandler = new SensorUpdateCurrentReadingRequestHandler(
            $sensorRepository,
            $sensorReadingTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );

        $logger = $this->createMock(LoggerInterface::class);

        $logger->expects(self::never())->method('error');
        $logger->expects(self::once())->method('info');

        $this->sut = new \App\AMQP\Device\Consumers\SensorSendUpdateReadingRequestConsumer(
            $logger,
            $sensorUpdateCurrentReadingRequestHandler,
        );

        $result = $this->sut->execute($amqpMess);

        self::assertTrue($result);
    }

    public function test_unsuccessful_http_response_returns_false(): void
    {
        $boolCurrentReadingUpdateDTO = new BoolCurrentReadingUpdateDTO(
            GenericRelay::NAME,
            1,
        );
        /** @var SensorRepository $sensorRepository */
        $sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
        /** @var RelayRepository $relayRepository */
        $relayRepository = $this->diContainer->get(RelayRepository::class);
        /** @var Relay $genericSensorType */
        $sensorID = $relayRepository->findAll()[0]->getBaseReadingType()->getSensor()->getSensorID();
        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateTransportMessageDTO(
            $sensorID,
            $boolCurrentReadingUpdateDTO,
        );

        $amqpMess = new AMQPMessage(serialize($requestSensorCurrentReadingUpdateMessageDTO));

        $sensorReadingTypeRepositoryFactory = $this->diContainer->get(SensorReadingTypeRepositoryFactory::class);
        $deviceSensorRequestArgumentBuilderFactory = $this->diContainer->get(DeviceSensorRequestArgumentBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::atLeastOnce())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );
        $sensorUpdateCurrentReadingRequestHandler = new SensorUpdateCurrentReadingRequestHandler(
            $sensorRepository,
            $sensorReadingTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );

        $logger = $this->createMock(LoggerInterface::class);

        $logger->expects(self::once())->method('error');
        $logger->expects(self::never())->method('info');

        $this->sut = new \App\AMQP\Device\Consumers\SensorSendUpdateReadingRequestConsumer(
            $logger,
            $sensorUpdateCurrentReadingRequestHandler,
        );

        $result = $this->sut->execute($amqpMess);

        self::assertFalse($result);
    }

    public function test_sending_sensor_type_not_allowed_returns_true(): void
    {
        $boolCurrentReadingUpdateDTO = new BoolCurrentReadingUpdateDTO(
            GenericMotion::NAME,
            1,
        );
        /** @var SensorRepository $sensorRepository */
        $sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
        /** @var RelayRepository $sensorTypeRepository */
        $sensorTypeRepository = $this->diContainer->get(MotionRepository::class);
        $sensorID = $sensorTypeRepository->findAll()[0]->getBaseReadingType()->getSensor()->getSensorID();
        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateTransportMessageDTO(
            $sensorID,
            $boolCurrentReadingUpdateDTO,
        );
        $amqpMess = new AMQPMessage(serialize($requestSensorCurrentReadingUpdateMessageDTO));

        $sensorReadingTypeRepositoryFactory = $this->diContainer->get(SensorReadingTypeRepositoryFactory::class);
        $deviceSensorRequestArgumentBuilderFactory = $this->diContainer->get(DeviceSensorRequestArgumentBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $httpClient = new MockHttpClient($response);

        $mockLogger = $this->createMock(LoggerInterface::class);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );
        $sensorUpdateCurrentReadingRequestHandler = new SensorUpdateCurrentReadingRequestHandler(
            $sensorRepository,
            $sensorReadingTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );

        $logger = $this->createMock(LoggerInterface::class);

        $logger->expects(self::once())->method('error');
        $logger->expects(self::never())->method('info');

        $this->sut = new \App\AMQP\Device\Consumers\SensorSendUpdateReadingRequestConsumer(
            $logger,
            $sensorUpdateCurrentReadingRequestHandler,
        );

        $result = $this->sut->execute($amqpMess);

        self::assertTrue($result);
    }
}
