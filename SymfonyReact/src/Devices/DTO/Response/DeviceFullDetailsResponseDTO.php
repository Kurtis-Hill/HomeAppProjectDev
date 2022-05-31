<?php

namespace App\Devices\DTO\Response;

use App\Devices\Entity\Devices;
use App\User\DTO\ResponseDTOs\GroupDTOs\GroupNameResponseDTO;
use App\User\DTO\ResponseDTOs\RoomDTOs\RoomResponseDTO;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class DeviceFullDetailsResponseDTO
{
    private int $deviceNameID;

    private string $deviceName;

    private ?string $secret;

    private GroupNameResponseDTO $groupName;

    private RoomResponseDTO $room;

    private ?string $ipAddress;

    private ?string $externalIpAddress;

    private array $roles;

    public function __construct(
        int $deviceNameID,
        string $deviceName,
        ?string $secret,
        GroupNameResponseDTO $groupName,
        RoomResponseDTO $room,
        ?string $ipAddress,
        ?string $externalIpAddress,
        array $roles
    ) {
        $this->deviceNameID = $deviceNameID;
        $this->deviceName = $deviceName;
        $this->secret = $secret;
        $this->groupName = $groupName;
        $this->room = $room;
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

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function getGroupName(): GroupNameResponseDTO
    {
        return $this->groupName;
    }

    public function getRoom(): RoomResponseDTO
    {
        return $this->room;
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
    public function getRoles(): array
    {
        return $this->roles;
    }
}
