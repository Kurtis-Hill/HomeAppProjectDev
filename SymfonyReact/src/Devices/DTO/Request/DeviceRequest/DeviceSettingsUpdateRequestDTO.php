<?php

namespace App\Devices\DTO\Request\DeviceRequest;

use App\Devices\DeviceServices\Request\DeviceSettingsUpdateRequestHandler;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class DeviceSettingsUpdateRequestDTO implements DeviceRequestDTOInterface
{
    public function __construct(
        private string $userName,
        private ?string $password,
    ) {}

    public function getUserName(): string
    {
        return $this->userName;
    }

    #[Groups([DeviceSettingsUpdateRequestHandler::WIFI_PASSWORD_GROUP])]
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
