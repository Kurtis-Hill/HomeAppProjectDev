<?php

namespace App\Devices\DTO\Internal;

use App\Devices\Entity\Devices;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Security\Core\User\UserInterface;

#[Immutable]
class NewDeviceDTO
{
    private UserInterface $createdBy;

    private GroupNames  $groupNameId;

    private Room  $roomId;

    private ?string $deviceName;

    private Devices $devices;

    private string $devicePassword;

    public function __construct(
        UserInterface $createdBy,
        GroupNames $groupNameId,
        Room $roomId,
        ?string $deviceName,
        string $devicePassword,
        Devices $devices,
    ) {
        $this->createdBy = $createdBy;
        $this->groupNameId = $groupNameId;
        $this->roomId = $roomId;
        $this->deviceName = $deviceName;
        $this->devicePassword = $devicePassword;
        $this->devices = $devices;
    }

    public function getDeviceName(): ?string
    {
        return $this->deviceName;
    }

    public function getDevicePassword(): string
    {
        return $this->devicePassword;
    }

    public function getGroupNameObject(): GroupNames
    {
        return $this->groupNameId;
    }

    public function getRoomObject(): Room
    {
        return $this->roomId;
    }

    public function getCreatedByUserObject(): UserInterface
    {
        return $this->createdBy;
    }

    public function getNewDevice(): Devices
    {
        return $this->devices;
    }
}
