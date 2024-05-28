<?php
declare(strict_types=1);

namespace App\Devices\DTO\Request\DeviceRequest;

use App\Devices\Builders\Request\DeviceWifiSettingsDTOBuilder;
use Symfony\Component\Serializer\Annotation\Groups;

readonly class DeviceWifiSettingsDTO
{
    public function __construct(
        private string $ssid,
        private string $password,
    ) {}

    #[Groups([DeviceWifiSettingsDTOBuilder::WIFI_CREDENTIALS])]
    public function getSsid(): string
    {
        return $this->ssid;
    }

    #[Groups([DeviceWifiSettingsDTOBuilder::WIFI_CREDENTIALS])]
    public function getPassword(): string
    {
        return $this->password;
    }
}
