<?php

namespace App\Devices\DTO\Response;

use App\Devices\Entity\Devices;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class DeviceResponseDTO
{
    public function __construct(
        private ?int $deviceID,
        private string $deviceName,
        private int $groupID,
        private int $roomID,
        private string|int $createdBy,
        private ?string $secret = null,
        private ?string $ipAddress = null,
        private ?string $externalIpAddress = null,
        private ?array $roles = []
    ) {
    }

    public function getDeviceID(): int
    {
        return $this->deviceID;
    }

    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    public function getGroupID(): int
    {
        return $this->groupID;
    }

    public function getRoomID(): int
    {
        return $this->roomID;
    }

    public function getCreatedBy(): string|int
    {
        return $this->createdBy;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getExternalIpAddress(): ?string
    {
        return $this->externalIpAddress;
    }

    #[ArrayShape([Devices::ROLE])]
    public function getRoles(): ?array
    {
        return $this->roles;
    }
}
