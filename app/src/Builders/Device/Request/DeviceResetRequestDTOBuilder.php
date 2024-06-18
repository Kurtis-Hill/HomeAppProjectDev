<?php

namespace App\Builders\Device\Request;

use App\DTOs\Device\Request\DeviceRequest\DeviceResetRequestDTO;

class DeviceResetRequestDTOBuilder
{
    public function buildResetRequestDTO(): DeviceResetRequestDTO
    {
        return new DeviceResetRequestDTO();
    }
}
