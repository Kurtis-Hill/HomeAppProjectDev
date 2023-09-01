<?php

namespace App\Tests\Devices\AMQP\Consumers;

use App\Common\Services\DeviceRequestHandler;
use App\Devices\AMQP\Consumers\SensorSendUpdateReadingRequestConsumer;
use App\Devices\Factories\DeviceSensorRequestArgumentBuilderFactory;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Repository\ReadingType\ORM\RelayRepository;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\Sensors\Repository\Sensors\ORM\SensorTypeRepository;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\SensorReadingUpdate\RequestReading\SensorUpdateCurrentReadingRequestHandler;
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
        $this->sut = $this->diContainer->get(SensorSendUpdateReadingRequestConsumer  ::class);
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
        /** @var SensorRepository $sensorRepository */
        $sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
        /** @var SensorTypeRepository $sensorTypeRepository */
        $sensorTypeRepository = $this->diContainer->get(SensorTypeRepository::class);
        /** @var GenericRelay $genericSensorType */
        $genericSensorType = $sensorTypeRepository->findOneBy(['sensorType' => GenericRelay::NAME]);
        $sensorID = $sensorRepository->findBy(['sensorTypeID' => $genericSensorType])[0]->getSensorID();
        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateMessageDTO(
            $sensorID,
            $boolCurrentReadingUpdateDTO,
        );

        $amqpMess = new AMQPMessage(serialize($requestSensorCurrentReadingUpdateMessageDTO));

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
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
            $sensorTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );

        $logger = $this->createMock(LoggerInterface::class);

        $logger->expects(self::never())->method('error');
        $logger->expects(self::once())->method('info');

        $this->sut = new SensorSendUpdateReadingRequestConsumer(
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
        /** @var SensorTypeRepository $sensorTypeRepository */
        $sensorTypeRepository = $this->diContainer->get(SensorTypeRepository::class);
        /** @var GenericRelay $genericSensorType */
        $genericSensorType = $sensorTypeRepository->findOneBy(['sensorType' => GenericRelay::NAME]);
        $sensorID = $sensorRepository->findBy(['sensorTypeID' => $genericSensorType])[0]->getSensorID();
        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateMessageDTO(
            $sensorID,
            $boolCurrentReadingUpdateDTO,
        );

        $amqpMess = new AMQPMessage(serialize($requestSensorCurrentReadingUpdateMessageDTO));

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
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
            $sensorTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );

        $logger = $this->createMock(LoggerInterface::class);

        $logger->expects(self::once())->method('error');
        $logger->expects(self::never())->method('info');

        $this->sut = new SensorSendUpdateReadingRequestConsumer(
            $logger,
            $sensorUpdateCurrentReadingRequestHandler,
        );

        $result = $this->sut->execute($amqpMess);

        self::assertFalse($result);
    }

    public function test_sending_sensor_type_not_allowed_returns_true(): void
    {
        $boolCurrentReadingUpdateDTO = new BoolCurrentReadingUpdateDTO(
            GenericRelay::NAME,
            1,
        );
        /** @var SensorRepository $sensorRepository */
        $sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
        /** @var SensorTypeRepository $sensorTypeRepository */
        $sensorTypeRepository = $this->diContainer->get(SensorTypeRepository::class);
        /** @var GenericRelay $genericSensorType */
        $genericSensorType = $sensorTypeRepository->findOneBy(['sensorType' => GenericMotion::NAME]);
        $sensorID = $sensorRepository->findBy(['sensorTypeID' => $genericSensorType])[0]->getSensorID();
        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateMessageDTO(
            $sensorID,
            $boolCurrentReadingUpdateDTO,
        );
        $amqpMess = new AMQPMessage(serialize($requestSensorCurrentReadingUpdateMessageDTO));

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);
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
            $sensorTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );

        $logger = $this->createMock(LoggerInterface::class);

        $logger->expects(self::once())->method('error');
        $logger->expects(self::never())->method('info');

        $this->sut = new SensorSendUpdateReadingRequestConsumer(
            $logger,
            $sensorUpdateCurrentReadingRequestHandler,
        );

        $result = $this->sut->execute($amqpMess);

        self::assertTrue($result);
    }
}
