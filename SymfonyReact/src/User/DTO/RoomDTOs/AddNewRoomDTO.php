<?php

namespace App\User\DTO\RoomDTOs;

class AddNewRoomDTO
{
    private string $roomName;

    private int $groupNameId;

    public function __construct(string $roomName, int $groupNameId)
    {
        $this->roomName = $roomName;
        $this->groupNameId = $groupNameId;
    }

    /**
     * @return string
     */
    public function getRoomName(): string
    {
        return $this->roomName;
    }

    /**
     * @return int
     */
    public function getGroupNameId(): int
    {
        return $this->groupNameId;
    }
}
