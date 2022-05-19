<?php

namespace App\Devices\Builders;

use App\Devices\DTO\Response\NewDeviceSuccessResponseDTO;

class NewDeviceDTOBuilder
{
    public static function buildNewDeviceSecretDTO(string $secret, $deviceID): NewDeviceSuccessResponseDTO
    {
        return new NewDeviceSuccessResponseDTO($secret, $deviceID);
    }
}
