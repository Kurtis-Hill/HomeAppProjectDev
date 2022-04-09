<?php

namespace App\User\DTO\InternalDTOs\RoomDTOs;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class AddNewRoomDTO
{
    private string $roomName;

    private int $groupNameId;

    public function __construct(string $roomName, int $groupNameId)
    {
        $this->roomName = $roomName;
        $this->groupNameId = $groupNameId;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }

    public function getGroupNameId(): int
    {
        return $this->groupNameId;
    }
}
