<?php

namespace App\Devices\DTO\Request\DeviceRequest;

use App\Devices\DeviceServices\Request\DeviceSettingsUpdateRequestHandler;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class DeviceLoginCredentialsUpdateRequestDTO
{
    public function __construct(
        private string $userName,
        private ?string $password,
    ) {}

    #[Groups([
        DeviceSettingsUpdateRequestHandler::PASSWORD_PRESENT,
        DeviceSettingsUpdateRequestHandler::PASSWORD_NOT_PRESENT
    ])]
    public function getUserName(): string
    {
        return $this->userName;
    }

    #[Groups([DeviceSettingsUpdateRequestHandler::PASSWORD_PRESENT])]
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
