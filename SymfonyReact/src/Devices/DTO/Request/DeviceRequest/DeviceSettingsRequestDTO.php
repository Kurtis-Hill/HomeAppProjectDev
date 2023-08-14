<?php

namespace App\Devices\DTO\Request\DeviceRequest;

use App\Devices\Builders\Request\DeviceSettingsRequestDTOBuilder;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class DeviceSettingsRequestDTO implements DeviceRequestDTOInterface
{
    public function __construct(
        private ?DeviceLoginCredentialsUpdateRequestDTO $deviceCredentials = null,
        private ?DeviceWifiSettingsDTO $wifi = null,
        private ?array $sensorData = null,
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
    public function getSensorData(): ?array
    {
        return $this->sensorData;
    }
}
