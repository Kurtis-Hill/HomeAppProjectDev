<?php
declare(strict_types=1);

namespace App\Builders\Device\Request;

use App\DTOs\Device\Request\DeviceRequest\DeviceWifiSettingsDTO;

class DeviceWifiSettingsDTOBuilder
{
    public const WIFI_CREDENTIALS = 'wifiCredentials';

    public static function buildDeviceWifiSettingsDTO(
        string $ssid,
        string $password,
    ): DeviceWifiSettingsDTO {
        return new DeviceWifiSettingsDTO(
            $ssid,
            $password,
        );
    }
}
