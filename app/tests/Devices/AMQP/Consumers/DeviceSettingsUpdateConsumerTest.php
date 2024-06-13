<?php

namespace App\Tests\Devices\AMQP\Consumers;

use App\AMQP\Device\Consumers\DeviceSettingsUpdateConsumer;
use App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder;
use App\DTOs\Device\Internal\DeviceSettingsUpdateDTO;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Services\Device\Request\DeviceRequestHandler;
use App\Services\Device\Request\DeviceSettingsUpdateRequestHandler;
use Doctrine\ORM\EntityManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class DeviceSettingsUpdateConsumerTest extends KernelTestCase
{
    private DeviceSettingsUpdateConsumer $sut;

    private ?EntityManagerInterface $entityManager;

    private DeviceRepositoryInterface $deviceRepository;

    private ContainerAwareInterface|Container $diContainer;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();

        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->deviceRepository = $this->diContainer->get(DeviceRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_http_code_not_200_returns_false(): void
    {
        $device = $this->deviceRepository->findAll()[0];
        $requestDTO = new DeviceSettingsUpdateDTO(
            $device->getDeviceID(),
            'username',
            'password',
        );
        $amqpMess = new AMQPMessage(serialize($requestDTO));

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::once())->method('error');
        $mockLogger->expects(self::once())->method('info');

        $response = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $httpClient = new MockHttpClient($response);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $deviceSettingsUpdateRequestHandler = new DeviceSettingsUpdateRequestHandler(
            $deviceRequestHandler,
            $this->deviceRepository,
            $deviceSettingsRequestDTOBuilder,
        );

        $this->sut = new DeviceSettingsUpdateConsumer(
            $deviceSettingsUpdateRequestHandler,
            $mockLogger,
        );

        $result = $this->sut->execute($amqpMess);

        self::assertFalse($result);
    }

    public function test_http_code_200_returns_true(): void
    {
        $device = $this->deviceRepository->findAll()[0];
        $requestDTO = new DeviceSettingsUpdateDTO(
            $device->getDeviceID(),
            'username',
            'password',
        );
        $amqpMess = new AMQPMessage(serialize($requestDTO));

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::atLeast(2))->method('info');
        $mockLogger->expects(self::never())->method('error');

        $response = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $httpClient = new MockHttpClient($response);

        $deviceRequestHandler = new DeviceRequestHandler(
            $httpClient,
            $mockLogger,
        );

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $deviceSettingsUpdateRequestHandler = new DeviceSettingsUpdateRequestHandler(
            $deviceRequestHandler,
            $this->deviceRepository,
            $deviceSettingsRequestDTOBuilder,
        );

        $this->sut = new DeviceSettingsUpdateConsumer(
            $deviceSettingsUpdateRequestHandler,
            $mockLogger,
        );

        $result = $this->sut->execute($amqpMess);

        self::assertTrue($result);
    }
}
