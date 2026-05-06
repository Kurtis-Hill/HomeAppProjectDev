<?php
declare(strict_types=1);

namespace App\Builders\Device\Request;

use App\DTOs\Device\Request\DeviceRequest\DeviceLoginCredentialsUpdateRequestDTO;
use App\DTOs\Device\Request\DeviceRequest\DeviceSettingsRequestDTO;
use App\DTOs\Device\Request\DeviceRequest\DeviceWifiSettingsDTO;
use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SensorTypeDataRequestEncapsulationDTO;

class DeviceSettingsRequestDTOBuilder
{
    public const DEVICE_CREDENTIALS = 'deviceCredentials';

    public const WIFI = 'wifi';

    public const SENSOR_DATA = 'sensorData';

    public function buildDeviceSettingsRequestDTO(
        ?DeviceLoginCredentialsUpdateRequestDTO $deviceCredentials = null,
        ?DeviceWifiSettingsDTO $wifi = null,
        ?SensorTypeDataRequestEncapsulationDTO $sensorData = null,
    ): DeviceSettingsRequestDTO {
        return new DeviceSettingsRequestDTO(
            $deviceCredentials,
            $wifi,
            $sensorData,
        );
    }
}
