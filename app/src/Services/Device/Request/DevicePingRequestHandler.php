<?php
declare(strict_types=1);

namespace App\Services\Device\Request;

use App\Builders\Device\Request\DevicePingRequestDTOBuilder;
use App\Entity\Device\Devices;
use App\Exceptions\Device\DeviceIPNotSetException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class DevicePingRequestHandler
{
    private const PING_ENDPOINT = 'ping';

    public function __construct(
        private DeviceRequestHandlerInterface $deviceRequestHandler,
        private DevicePingRequestDTOBuilder $devicePingRequestDTOBuilder,
    ) {}

    /**
     * @throws \App\Exceptions\Device\DeviceIPNotSetException
     * @throws TransportExceptionInterface
     */
    public function pingDevice(Devices $device): bool
    {
        $devicePingRequestDTO = $this->devicePingRequestDTOBuilder->buildPingRequestDTO();
        $deviceRequestEncapsulationDTO = \App\Builders\Device\Request\DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
            $device,
            $devicePingRequestDTO,
            self::PING_ENDPOINT
        );

        $response = $this->deviceRequestHandler->handleDeviceRequest($deviceRequestEncapsulationDTO);

        return $response->getStatusCode() === Response::HTTP_OK;
    }
}
