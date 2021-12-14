<?php

namespace App\Devices\DTO;

use App\Entity\Core\User;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class NewDeviceDTO
{
    private User  $createdBy;

    private GroupNames  $groupNameId;

    private Room  $roomId;

    private ?string $deviceName;

    public function __construct(
        User $createdBy,
        GroupNames $groupNameId,
        Room $roomId,
        ?string $deviceName,
    ) {
        $this->createdBy = $createdBy;
        $this->groupNameId = $groupNameId;
        $this->roomId = $roomId;
        $this->deviceName = $deviceName;
    }

    public function getDeviceName(): ?string
    {
        return $this->deviceName;
    }

    public function getGroupNameObject(): GroupNames
    {
        return $this->groupNameId;
    }

    public function getRoomObject(): Room
    {
        return $this->roomId;
    }

    public function getCreatedByUserObject(): User
    {
        return $this->createdBy;
    }
}
