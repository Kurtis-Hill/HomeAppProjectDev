<?php

namespace App\Devices\DeviceServices\Request;

use App\Common\Services\DeviceRequestHandlerInterface;
use App\Devices\Builders\Request\DevicePingRequestDTOBuilder;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Entity\Devices;
use Symfony\Component\HttpFoundation\Response;

readonly class DevicePingRequestHandler
{
    private const PING_ENDPOINT = 'ping';

    public function __construct(
        private DeviceRequestHandlerInterface $deviceRequestHandler,
        private DevicePingRequestDTOBuilder $devicePingRequestDTOBuilder,
    ) {}

    public function pingDevice(Devices $device): bool
    {
        $devicePingRequestDTO = $this->devicePingRequestDTOBuilder->buildPingRequestDTO();
        $deviceRequestEncapsulationDTO = DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
            $device,
            $devicePingRequestDTO,
            self::PING_ENDPOINT
        );

        $response = $this->deviceRequestHandler->handleDeviceRequest($deviceRequestEncapsulationDTO);

        return $response->getStatusCode() === Response::HTTP_OK;
    }
}
