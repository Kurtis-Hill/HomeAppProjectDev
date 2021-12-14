<?php

namespace App\Devices\DTO;

use App\Entity\Core\User;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class DeviceDTO
{
    private User|int  $createdBy;

    private GroupNames|int  $groupNameId;

    private Room|int  $roomId;

    private ?string $deviceName;

    public function __construct(
        User|int $createdBy,
        GroupNames|int $groupNameId,
        Room|int $roomId,
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

    public function getGroupNameId(): GroupNames|int
    {
        return $this->groupNameId;
    }

    public function getRoomId(): Room|int
    {
        return $this->roomId;
    }

    public function getCreatedBy(): User|int
    {
        return $this->createdBy;
    }
}
