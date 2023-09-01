<?php

namespace App\Common\Services;

use App\Devices\DTO\Request\DeviceRequest\DeviceRequestEncapsulationDTO;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface DeviceRequestHandlerInterface
{
    public function handleDeviceRequest(DeviceRequestEncapsulationDTO $deviceRequestEncapsulationDTO, array $groups = []): ResponseInterface;
}
