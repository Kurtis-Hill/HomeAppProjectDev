<?php

namespace App\Devices\DTO\Response;

use App\Devices\Entity\Devices;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class DeviceResponseDTO
{
    private ?int $deviceNameID;

    private string $deviceName;

    private ?string $secret;

    private int $groupNameID;

    private int $roomID;

    private string|int $createdBy;

    private ?string $ipAddress;

    private ?string $externalIpAddress;

    private array $roles;

    public function __construct(
        ?int $deviceNameID,
        string $deviceName,
        int $groupNameID,
        int $roomID,
        string|int $createdBy,
        ?string $secret = null,
        ?string $ipAddress = null,
        ?string $externalIpAddress = null,
        ?array $roles = []
    ) {
        $this->deviceNameID = $deviceNameID;
        $this->deviceName = $deviceName;
        $this->secret = $secret;
        $this->groupNameID = $groupNameID;
        $this->roomID = $roomID;
        $this->createdBy = $createdBy;
        $this->ipAddress = $ipAddress;
        $this->externalIpAddress = $externalIpAddress;
        $this->roles = $roles;
    }

    public function getDeviceNameID(): int
    {
        return $this->deviceNameID;
    }

    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    public function getGroupNameID(): int
    {
        return $this->groupNameID;
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
