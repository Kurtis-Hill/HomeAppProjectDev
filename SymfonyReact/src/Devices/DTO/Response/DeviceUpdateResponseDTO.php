<?php

namespace App\Devices\DTO\Response;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class DeviceUpdateResponseDTO
{
    private string $deviceName;

    private string $roomName;

    private int $roomID;

    private string $groupName;

    private int $groupNameID;

    public function __construct(
        string $deviceName,
        string $roomName,
        int $roomID,
        string $groupName,
        int $groupNameID,
    ) {
        $this->deviceName = $deviceName;
        $this->roomName = $roomName;
        $this->roomID = $roomID;
        $this->groupName = $groupName;
        $this->groupNameID = $groupNameID;
    }

    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getGroupNameID(): int
    {
        return $this->groupNameID;
    }

    public function getRoomID(): int
    {
        return $this->roomID;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }
}
