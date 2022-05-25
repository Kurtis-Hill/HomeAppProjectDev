<?php

namespace App\User\DTO\InternalDTOs\RoomDTOs;

use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class AddNewRoomDTO
{
    private string $roomName;

    private GroupNames $groupNameID;

    private Room $room;

    public function __construct(string $roomName, GroupNames $groupNameID, Room $room)
    {
        $this->roomName = $roomName;
        $this->groupNameID = $groupNameID;
        $this->room = $room;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }

    public function getGroupNameID(): GroupNames
    {
        return $this->groupNameID;
    }

    public function getNewRoom(): Room
    {
        return $this->room;
    }
}
