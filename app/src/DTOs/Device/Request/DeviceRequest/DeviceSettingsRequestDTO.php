<?php
declare(strict_types=1);

namespace App\DTOs\Device\Request\DeviceRequest;

use App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder;
use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SensorTypeDataRequestEncapsulationDTO;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class DeviceSettingsRequestDTO implements DeviceRequestDTOInterface
{
    public function __construct(
        private ?DeviceLoginCredentialsUpdateRequestDTO $deviceCredentials = null,
        private ?DeviceWifiSettingsDTO $wifi = null,
        private ?SensorTypeDataRequestEncapsulationDTO $sensorData = null,
    ) {}

    #[Groups([DeviceSettingsRequestDTOBuilder::DEVICE_CREDENTIALS])]
    public function getDeviceCredentials(): ?DeviceLoginCredentialsUpdateRequestDTO
    {
        return $this->deviceCredentials;
    }

    #[Groups([DeviceSettingsRequestDTOBuilder::WIFI])]
    public function getWifi(): ?DeviceWifiSettingsDTO
    {
        return $this->wifi;
    }

    #[Groups([DeviceSettingsRequestDTOBuilder::SENSOR_DATA])]
    public function getSensorData(): ?SensorTypeDataRequestEncapsulationDTO
    {
        return $this->sensorData;
    }
}
