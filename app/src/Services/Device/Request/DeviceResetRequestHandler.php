<?php
declare(strict_types=1);

namespace App\Services\Device\Request;

use App\Builders\Device\Request\DeviceRequestEncapsulationBuilder;
use App\Builders\Device\Request\DeviceResetRequestDTOBuilder;
use App\Entity\Device\Devices;
use App\Exceptions\Device\DeviceIPNotSetException;
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
