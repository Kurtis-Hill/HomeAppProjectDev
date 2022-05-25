<?php

namespace App\User\DTO\ResponseDTOs\RoomDTOs;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class RoomResponseDTO
{
    private int $roomID;

    private string $roomName;

    private int $groupNameID;

    public function __construct(
      int $roomID,
      string $roomName,
      int $groupNameID,
    ) {
        $this->roomID = $roomID;
        $this->roomName = $roomName;
        $this->groupNameID = $groupNameID;
    }

    public function getRoomID(): int
    {
        return $this->roomID;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }

    public function getGroupNameID(): int
    {
        return $this->groupNameID;
    }
}
