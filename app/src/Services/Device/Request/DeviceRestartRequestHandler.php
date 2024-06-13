<?php
declare(strict_types=1);

namespace App\Services\Device\Request;

use App\Builders\Device\Request\DeviceRequestEncapsulationBuilder;
use App\Builders\Device\Request\DeviceRestartRequestDTOBuilder;
use App\Entity\Device\Devices;
use App\Exceptions\Device\DeviceIPNotSetException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class DeviceRestartRequestHandler
{
    private const RESTART_ENDPOINT = 'restart';

    public function __construct(
        private DeviceRequestHandlerInterface $deviceRequestHandler,
        private DeviceRestartRequestDTOBuilder $deviceRestartRequestDTOBuilder,
    ) {}

    /**
     * @throws \App\Exceptions\Device\DeviceIPNotSetException
     * @throws TransportExceptionInterface
     */
    public function restartDevice(Devices $device): bool
    {
        $deviceRestartRequestDTO = $this->deviceRestartRequestDTOBuilder->buildRestartRequestDTO();
        $deviceRequestEncapsulationDTO = DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
            $device,
            $deviceRestartRequestDTO,
            self::RESTART_ENDPOINT
        );

        $response = $this->deviceRequestHandler->handleDeviceRequest($deviceRequestEncapsulationDTO);

        return $response->getStatusCode() === Response::HTTP_OK;
    }
}
