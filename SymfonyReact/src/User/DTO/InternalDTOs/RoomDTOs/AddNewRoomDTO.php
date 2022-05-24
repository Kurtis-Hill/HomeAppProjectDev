<?php

namespace App\User\DTO\InternalDTOs\RoomDTOs;

use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class AddNewRoomDTO
{
    private string $roomName;

    private GroupNames $groupNameId;

    private Room $room;

    public function __construct(string $roomName, GroupNames $groupNameId)
    {
        $this->roomName = $roomName;
        $this->groupNameId = $groupNameId;
        $this->room = new Room();
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }

    public function getGroupNameId(): GroupNames
    {
        return $this->groupNameId;
    }

    public function getNewRoom(): Room
    {
        return $this->room;
    }
}
