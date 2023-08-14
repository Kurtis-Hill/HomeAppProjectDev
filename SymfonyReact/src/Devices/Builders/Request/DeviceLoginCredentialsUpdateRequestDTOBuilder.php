<?php

namespace App\Devices\Builders\Request;

use App\Devices\DTO\Request\DeviceRequest\DeviceRequestDTOInterface;
use App\Devices\DTO\Request\DeviceRequest\DeviceLoginCredentialsUpdateRequestDTO;

class DeviceLoginCredentialsUpdateRequestDTOBuilder
{
    public static function buildLoginCredentialsUpdateRequestDTO(
        string $username,
        ?string $password,
    ): DeviceLoginCredentialsUpdateRequestDTO {
        return new DeviceLoginCredentialsUpdateRequestDTO(
            $username,
            $password,
        );
    }
}
