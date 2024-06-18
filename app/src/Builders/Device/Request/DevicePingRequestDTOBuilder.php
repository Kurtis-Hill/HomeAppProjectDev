<?php

namespace App\Builders\Device\Request;

use App\DTOs\Device\Request\DeviceRequest\DevicePingRequestDTO;

class DevicePingRequestDTOBuilder
{
    public function buildPingRequestDTO(): DevicePingRequestDTO
    {
        return new DevicePingRequestDTO();
    }
}
