<?php

namespace App\Devices\DTO;

use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class NewDeviceCheckDTO
{
    private GroupNames $groupNames;

    private Room $room;

    public function __construct(GroupNames $groupNames, Room $room)
    {
        $this->groupNames = $groupNames;
        $this->room = $room;
    }

    public function getGroupNames(): GroupNames
    {
        return $this->groupNames;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }
}
