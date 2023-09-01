<?php

namespace App\Tests\Devices\DeviceServices;

use App\Common\Services\DeviceRequestHandler;
use App\Devices\Builders\Request\DeviceSettingsRequestDTOBuilder;
use App\Devices\DeviceServices\Request\DeviceSettingsUpdateRequestHandler;
use App\Devices\DTO\Internal\DeviceSettingsUpdateDTO;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\Exceptions\DeviceNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class DeviceSettingsUpdateRequestHandlerTest extends KernelTestCase
{
    private ContainerInterface|Container $diContainer;

    private ?EntityManagerInterface $entityManager;

    private ?DeviceRepositoryInterface $deviceRepository;

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

    public function test_passing_device_id_doesnt_exist_throws_exception(): void
    {
        $this->expectException(DeviceNotFoundException::class);

        while (true) {
            $randomID = random_int(1, 99900);
            $device = $this->deviceRepository->find($randomID);
            if ($device === null) {
                break;
            }
        }
        $deviceSettingsUpdateDTO = new DeviceSettingsUpdateDTO(
            $randomID,
            'username',
            'password',
        );

        $this->diContainer->get(DeviceSettingsUpdateRequestHandler::class)->handleDeviceSettingsUpdateRequest(
            $deviceSettingsUpdateDTO
        );
    }

    public function test_unsuccessful_response_code_returns_false(): void
    {
        /** @var Devices $device */
        $device = $this->deviceRepository->findAll()[0];

        $deviceSettingsUpdateDTO = new DeviceSettingsUpdateDTO(
            $device->getDeviceID(),
            'username',
            'password',
        );

        $mockResponse = new MockResponse([], ['http_code' => Response::HTTP_BAD_REQUEST]);
        $mockHttpClient = new MockHttpClient($mockResponse);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::once())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $mockHttpClient,
            $mockLogger,
        );

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $sut = new DeviceSettingsUpdateRequestHandler(
            $deviceRequestHandler,
            $this->deviceRepository,
            $deviceSettingsRequestDTOBuilder
        );

        self::assertFalse($sut->handleDeviceSettingsUpdateRequest($deviceSettingsUpdateDTO));
    }

    public function test_successful_response_code_returns_true(): void
    {
        /** @var Devices $device */
        $device = $this->deviceRepository->findAll()[0];

        $deviceSettingsUpdateDTO = new DeviceSettingsUpdateDTO(
            $device->getDeviceID(),
            'username',
            'password',
        );

        $mockResponse = new MockResponse([], ['http_code' => Response::HTTP_OK]);
        $mockHttpClient = new MockHttpClient($mockResponse);

        $mockLogger = $this->createMock(LoggerInterface::class);
        $mockLogger->expects(self::once())->method('info');

        $deviceRequestHandler = new DeviceRequestHandler(
            $mockHttpClient,
            $mockLogger,
        );

        $deviceSettingsRequestDTOBuilder = $this->diContainer->get(DeviceSettingsRequestDTOBuilder::class);

        $sut = new DeviceSettingsUpdateRequestHandler(
            $deviceRequestHandler,
            $this->deviceRepository,
            $deviceSettingsRequestDTOBuilder,
        );

        self::assertTrue($sut->handleDeviceSettingsUpdateRequest($deviceSettingsUpdateDTO));
    }
}
