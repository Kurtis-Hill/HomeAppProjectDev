<?php

namespace App\Devices\Builders\Request;

use App\Devices\DTO\Request\DeviceRequest\DevicePingRequestDTO;

class DevicePingRequestDTOBuilder
{
    public function buildPingRequestDTO(): DevicePingRequestDTO
    {
        return new DevicePingRequestDTO();
    }
}
