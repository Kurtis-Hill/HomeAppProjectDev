<?php
declare(strict_types=1);

namespace App\Devices\DTO\Internal;

readonly class DeviceSettingsUpdateDTO
{
    public function __construct(
        private int $deviceID,
        private ?string $username,
        private ?string $password,
    ) {}

    public function getDeviceID(): int
    {
        return $this->deviceID;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
