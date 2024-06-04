<?php

namespace App\Devices\Builders\Request;

use App\Devices\DTO\Request\DeviceRequest\DeviceRestartRequestDTO;

class DeviceRestartRequestDTOBuilder
{
    public function buildRestartRequestDTO(): DeviceRestartRequestDTO
    {
        return new DeviceRestartRequestDTO();
    }
}
