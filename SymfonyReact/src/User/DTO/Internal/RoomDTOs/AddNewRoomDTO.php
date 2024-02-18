<?php

namespace App\User\DTO\Internal\RoomDTOs;

use App\User\Entity\Group;
use App\User\Entity\Room;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
class AddNewRoomDTO
{
    private string $roomName;

    private Room $room;

    public function __construct(string $roomName, Room $room)
    {
        $this->roomName = $roomName;
        $this->room = $room;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }

    public function getNewRoom(): Room
    {
        return $this->room;
    }
}
