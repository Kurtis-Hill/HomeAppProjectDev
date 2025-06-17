<?php

namespace App\Builders\Device\Request;

use App\DTOs\Device\Request\DeviceRequest\DeviceRestartRequestDTO;

class DeviceRestartRequestDTOBuilder
{
    public function buildRestartRequestDTO(): DeviceRestartRequestDTO
    {
        return new DeviceRestartReqtuestDTO();
    }
}
