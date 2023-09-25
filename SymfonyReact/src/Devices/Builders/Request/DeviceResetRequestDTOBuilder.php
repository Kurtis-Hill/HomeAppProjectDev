<?php

namespace App\Devices\Builders\Request;

use App\Devices\DTO\Request\DeviceRequest\DeviceResetRequestDTO;

class DeviceResetRequestDTOBuilder
{
    public function buildResetRequestDTO(): DeviceResetRequestDTO
    {
        return new DeviceResetRequestDTO();
    }
}
