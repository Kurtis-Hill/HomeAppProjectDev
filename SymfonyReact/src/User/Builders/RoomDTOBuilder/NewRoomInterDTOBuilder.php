<?php

namespace App\User\Builders\RoomDTOBuilder;

use App\User\DTO\InternalDTOs\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;

class NewRoomInterDTOBuilder
{
    public static function buildInternalNewRoomDTO(
        string $roomName,
        GroupNames $groupNameID,
    ): AddNewRoomDTO {
        $room = new Room();

        return new AddNewRoomDTO(
            $roomName,
            $groupNameID,
            $room
        );
    }
}
