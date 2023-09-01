<?php

namespace App\Devices\Builders\Request;

use App\Devices\DTO\Request\DeviceRequest\DeviceWifiSettingsDTO;

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
