<?php
declare(strict_types=1);

namespace App\Devices\DeviceServices\Request;

use App\Common\Services\DeviceRequestHandlerInterface;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Builders\Request\DeviceResetRequestDTOBuilder;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceIPNotSetException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class DeviceResetRequestHandler
{
    private const RESET_ENDPOINT = 'reset';

    public function __construct(
        private DeviceRequestHandlerInterface $deviceRequestHandler,
        private DeviceResetRequestDTOBuilder $deviceResetRequestDTOBuilder,
    ) {
    }

    /**
     * @throws DeviceIPNotSetException
     * @throws TransportExceptionInterface
     */
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
