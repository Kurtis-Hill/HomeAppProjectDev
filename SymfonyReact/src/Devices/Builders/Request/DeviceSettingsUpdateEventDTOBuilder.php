<?php

namespace App\Devices\Builders\Request;

use App\Devices\DTO\Request\DeviceRequest\DeviceSettingsUpdateEventDTO;

class DeviceSettingsUpdateEventDTOBuilder
{
    public function buildDeviceSettingUpdateEventDTO(string $deviceName, string $devicePlainPassword): DeviceSettingsUpdateEventDTO
    {
        return new DeviceSettingsUpdateEventDTO(
            $deviceName,
            $devicePlainPassword,
        );
    }
}
