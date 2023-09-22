<?php

namespace App\Devices\DeviceServices\Request;

use App\Common\Services\DeviceRequestHandlerInterface;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Builders\Request\DeviceRestartRequestDTOBuilder;
use App\Devices\Entity\Devices;
use Symfony\Component\HttpFoundation\Response;

readonly class DeviceRestartRequestHandler
{
    private const RESTART_ENDPOINT = 'restart';

    public function __construct(
        private DeviceRequestHandlerInterface $deviceRequestHandler,
        private DeviceRestartRequestDTOBuilder $deviceRestartRequestDTOBuilder,
    ) {}

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
