<?php
declare(strict_types=1);

namespace App\Builders\Device\DeviceUpdate;

use App\DTOs\Device\Internal\DeviceSettingsUpdateDTO;

class DeviceSettingsUpdateDTOBuilder
{
    public function buildDeviceSettingUpdateEventDTO(
        int $deviceID,
        ?string $deviceName,
        ?string $devicePlainPassword
    ): DeviceSettingsUpdateDTO {
        return new DeviceSettingsUpdateDTO(
            $deviceID,
            $deviceName,
            $devicePlainPassword,
        );
    }
}
