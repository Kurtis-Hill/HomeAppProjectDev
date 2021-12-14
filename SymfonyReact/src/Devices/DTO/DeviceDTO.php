<?php

namespace App\Devices\DTO;

use App\Entity\Core\User;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;

class DeviceDTO
{
    private ?string $deviceName;

    private GroupNames $groupNameId;

    private Room $roomId;

    private User $createdBy;

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

    public function getGroupNameId(): GroupNames
    {
        return $this->groupNameId;
    }

    public function getRoomId(): Room
    {
        return $this->roomId;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }
}
