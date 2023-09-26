<?php

namespace App\Devices\DeviceServices\Request;

use App\Common\Services\DeviceRequestHandlerInterface;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Builders\Request\DeviceResetRequestDTOBuilder;
use App\Devices\Entity\Devices;
use Symfony\Component\HttpFoundation\Response;

class DeviceResetRequestHandler
{
    private const RESET_ENDPOINT = 'reset';

    public function __construct(
        private DeviceRequestHandlerInterface $deviceRequestHandler,
        private DeviceResetRequestDTOBuilder $deviceResetRequestDTOBuilder,
    ) {}

    public function resetDevice(Devices $device): bool
    {
        $deviceResetRequestDTO = $this->deviceResetRequestDTOBuilder->buildResetRequestDTO();
        $deviceRequestEncapsulationDTO = DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
            $device,
            $deviceResetRequestDTO,
            self::RESET_ENDPOINT
        );

        $response = $this->deviceRequestHandler->handleDeviceRequest($deviceRequestEncapsulationDTO);

        return $response->getStatusCode() === Response::HTTP_OK;
    }
}
