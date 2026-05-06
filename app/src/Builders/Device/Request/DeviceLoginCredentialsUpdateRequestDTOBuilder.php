<?php
declare(strict_types=1);

namespace App\Builders\Device\Request;

use App\DTOs\Device\Request\DeviceRequest\DeviceLoginCredentialsUpdateRequestDTO;

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
