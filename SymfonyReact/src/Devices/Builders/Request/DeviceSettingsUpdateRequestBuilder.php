<?php

namespace App\Devices\Builders\Request;

use App\Devices\DTO\Request\DeviceRequest\DeviceRequestDTOInterface;
use App\Devices\DTO\Request\DeviceRequest\DeviceSettingsUpdateRequestDTO;

class DeviceSettingsUpdateRequestBuilder
{
    public static function buildDeviceSettingsUpdateRequestDTO(
        string $username,
        ?string $password,
    ): DeviceRequestDTOInterface {
        return new DeviceSettingsUpdateRequestDTO(
            $username,
            $password,
        );
    }
}
