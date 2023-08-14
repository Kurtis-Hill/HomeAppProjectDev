<?php

namespace App\Devices\Builders\Request;

use App\Devices\DTO\Request\DeviceRequest\DeviceSettingsRequestDTO;
use App\Devices\DTO\Request\DeviceRequest\DeviceLoginCredentialsUpdateRequestDTO;
use App\Devices\DTO\Request\DeviceRequest\DeviceWifiSettingsDTO;

class DeviceSettingsRequestDTOBuilder
{
    public const DEVICE_CREDENTIALS = 'deviceCredentials';

    public const WIFI = 'wifi';

    public const SENSOR_DATA = 'sensorData';

    public function buildDeviceSettingsRequestDTO(
        ?DeviceLoginCredentialsUpdateRequestDTO $deviceCredentials = null,
        ?DeviceWifiSettingsDTO $wifi = null,
        ?array $sensorData = null,
    ): DeviceSettingsRequestDTO {
        return new DeviceSettingsRequestDTO(
            $deviceCredentials,
            $wifi,
            $sensorData,
        );
    }
}
