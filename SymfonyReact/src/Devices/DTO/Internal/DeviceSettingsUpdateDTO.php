<?php

namespace App\Devices\DTO\Internal;

readonly class DeviceSettingsUpdateDTO
{
    public function __construct(
        private int $deviceID,
        private ?string $userName,
        private ?string $password,
    ) {}

    public function getDeviceID(): int
    {
        return $this->deviceID;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
