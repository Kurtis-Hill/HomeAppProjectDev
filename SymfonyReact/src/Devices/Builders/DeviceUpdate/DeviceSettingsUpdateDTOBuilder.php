<?php

namespace App\Devices\Builders\DeviceUpdate;

use App\Devices\DTO\Internal\DeviceSettingsUpdateDTO;

class DeviceSettingsUpdateDTOBuilder
{
    public function buildDeviceSettingUpdateEventDTO(int $deviceID, ?string $deviceName, ?string $devicePlainPassword): DeviceSettingsUpdateDTO
    {
        return new DeviceSettingsUpdateDTO(
            $deviceID,
            $deviceName,
            $devicePlainPassword,
        );
    }
}
