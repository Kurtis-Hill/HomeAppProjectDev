<?php

namespace App\Tests\Sensors\SensorService\SensorReadingUpdate\RequestReading;

use App\Common\Services\DeviceRequestHandler;
use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Devices\Factories\DeviceSensorRequestArgumentBuilderFactory;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Repository\ReadingType\ORM\MotionRepository;
use App\Sensors\Repository\ReadingType\ORM\RelayRepository;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\Sensors\SensorServices\SensorReadingUpdate\RequestReading\SensorUpdateCurrentReadingRequestHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class SensorUpdateCurrentReadingRequestHandlerTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    private ?RelayRepository $relayRepository;

    private ContainerInterface|Container $diContainer;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();
        $this->relayRepository = $this->diContainer->get(RelayRepository::class);
        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_passing_correct_data_returns_true(): void
    {
        $sensorRepository = $this->diContainer->get(SensorRepository::class);

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);

        $deviceSensorRequestArgumentBuilderFactory = $this->diContainer->get(DeviceSensorRequestArgumentBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $httpClient = new MockHttpClient($response);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::once())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
          $httpClient,
            $mockLogger,
        );

        $sut = new SensorUpdateCurrentReadingRequestHandler(
            $sensorRepository,
            $sensorTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );

        $relay = $this->relayRepository->findAll()[0];

        $boolCurrentReadingUpdateRequestDTO = new BoolCurrentReadingUpdateDTO(
            GenericRelay::NAME,
            !$relay->getCurrentReading(),
        );

        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateMessageDTO(
            $relay->getSensorID(),
            $boolCurrentReadingUpdateRequestDTO,
        );

        $result = $sut->handleUpdateSensorReadingRequest($requestSensorCurrentReadingUpdateMessageDTO);
        self::assertTrue($result);

        $relayAfterUpdate = $this->relayRepository->findOneBy(['sensor' => $relay->getSensorID()]);
        self::assertEquals(
            $boolCurrentReadingUpdateRequestDTO->getCurrentReading(),
            $relayAfterUpdate->getCurrentReading(),
        );
    }

    public function test_sensor_not_exist_throws_exception(): void
    {
        $sensorRepository = $this->diContainer->get(SensorRepository::class);

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);

        $deviceSensorRequestArgumentBuilderFactory = $this->diContainer->get(DeviceSensorRequestArgumentBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => 200]);
        $httpClient = new MockHttpClient($response);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::never())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );

        $sut = new SensorUpdateCurrentReadingRequestHandler(
            $sensorRepository,
            $sensorTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );


        while (true) {
            $sensorID = random_int(1, 100000);
            $sensor = $sensorRepository->find($sensorID);
            if ($sensor === null) {
                break;
            }
        }

        $boolCurrentReadingUpdateRequestDTO = new BoolCurrentReadingUpdateDTO(
            GenericRelay::NAME,
            false,
        );

        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateMessageDTO(
            $sensorID,
            $boolCurrentReadingUpdateRequestDTO,
        );

        $this->expectException(SensorNotFoundException::class);
        $sut->handleUpdateSensorReadingRequest($requestSensorCurrentReadingUpdateMessageDTO);
    }

    public function test_no_device_local_ip_throws_exception(): void
    {
        $sensorRepository = $this->diContainer->get(SensorRepository::class);

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);

        $deviceSensorRequestArgumentBuilderFactory = $this->diContainer->get(DeviceSensorRequestArgumentBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => 200]);
        $httpClient = new MockHttpClient($response);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::never())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );

        $sut = new SensorUpdateCurrentReadingRequestHandler(
            $sensorRepository,
            $sensorTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );

        $relay = $this->relayRepository->findAll()[0];

        $device = $relay->getSensor()->getDevice();
        $device->setIpAddress(null);
        $this->relayRepository->flush();

        $boolCurrentReadingUpdateRequestDTO = new BoolCurrentReadingUpdateDTO(
            GenericRelay::NAME,
            !$relay->getCurrentReading(),
        );

        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateMessageDTO(
            $relay->getSensorID(),
            $boolCurrentReadingUpdateRequestDTO,
        );

        $this->expectException(DeviceIPNotSetException::class);

        $sut->handleUpdateSensorReadingRequest($requestSensorCurrentReadingUpdateMessageDTO);
    }

    public function test_argument_builder_throws_exception_when_no_matching_argument(): void
    {
        $sensorRepository = $this->diContainer->get(SensorRepository::class);

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);

        $deviceSensorRequestArgumentBuilderFactory = $this->diContainer->get(DeviceSensorRequestArgumentBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => 200]);
        $httpClient = new MockHttpClient($response);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::never())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );

        $sut = new SensorUpdateCurrentReadingRequestHandler(
            $sensorRepository,
            $sensorTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );
        $motionRepository = $this->diContainer->get(MotionRepository::class);
        $motion = $motionRepository->findAll()[0];

        $boolCurrentReadingUpdateRequestDTO = new BoolCurrentReadingUpdateDTO(
            GenericMotion::NAME,
            !$motion->getCurrentReading(),
        );

        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateMessageDTO(
            $motion->getSensorID(),
            $boolCurrentReadingUpdateRequestDTO,
        );

        $this->expectException(SensorTypeException::class);

        $sut->handleUpdateSensorReadingRequest($requestSensorCurrentReadingUpdateMessageDTO);
    }

//    public function test_sending_sensor_that_cannot_be_processed_by_request_handler(): void
//    {
//
//    }

    public function test_current_reading_doesnt_change_when_request_fails(): void
    {
        $sensorRepository = $this->diContainer->get(SensorRepository::class);

        $sensorTypeRepositoryFactory = $this->diContainer->get(SensorTypeRepositoryFactory::class);

        $deviceSensorRequestArgumentBuilderFactory = $this->diContainer->get(DeviceSensorRequestArgumentBuilderFactory::class);

        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::once())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );

        $sut = new SensorUpdateCurrentReadingRequestHandler(
            $sensorRepository,
            $sensorTypeRepositoryFactory,
            $deviceSensorRequestArgumentBuilderFactory,
            $deviceRequestHandler,
        );

        $relay = $this->relayRepository->findAll()[0];

        $boolCurrentReadingUpdateRequestDTO = new BoolCurrentReadingUpdateDTO(
            GenericRelay::NAME,
            !$relay->getCurrentReading(),
        );

        $requestSensorCurrentReadingUpdateMessageDTO = new RequestSensorCurrentReadingUpdateMessageDTO(
            $relay->getSensorID(),
            $boolCurrentReadingUpdateRequestDTO,
        );

        $result = $sut->handleUpdateSensorReadingRequest($requestSensorCurrentReadingUpdateMessageDTO);
        self::assertFalse($result);

        $relayAfterUpdate = $this->relayRepository->findOneBy(['sensor' => $relay->getSensorID()]);
        self::assertNotSame(
            $boolCurrentReadingUpdateRequestDTO->getCurrentReading(),
            $relayAfterUpdate->getCurrentReading(),
        );
    }
}
