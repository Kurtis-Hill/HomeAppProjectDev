<?php

namespace App\Devices\DTO\Response;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class DeviceDTO
{
    private int $deviceNameID;

    private string $deviceName;

    private ?string $password;

    private int $groupNameID;

    private int $roomID;

    private string|int $createdBy;

    private ?string $ipAddress;

    private ?string $externalIpAddress;

    private ?array $roles;

    public function __construct(
        int $deviceNameID,
        string $deviceName,
        int $groupNameID,
        int $roomID,
        string|int $createdBy,
        ?string $password = null,
        ?string $ipAddress = null,
        ?string $externalIpAddress = null,
        ?array $roles = null
    ) {
        $this->deviceNameID = $deviceNameID;
        $this->deviceName = $deviceName;
        $this->password = $password;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getGroupNameID(): int
    {
        return $this->groupNameID;
    }

    public function getRoomID(): int
    {
        return $this->roomID;
    }

    public function getCreatedBy(): int|string
    {
        return $this->createdBy;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getExternalIpAddress(): ?string
    {
        return $this->externalIpAddress;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }
    }
