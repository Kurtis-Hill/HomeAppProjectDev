<?php

namespace App\Devices\DTO\Request\DeviceRequest;

readonly class DeviceSettingsUpdateEventDTO
{
    public function __construct(
        private string $userName,
        private string $password,
    ) {}

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
